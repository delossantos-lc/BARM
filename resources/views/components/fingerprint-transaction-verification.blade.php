@props([
    'rfidInputId',
    'borrowerIdInputId' => null,
    'borrowerNumberInputId' => null,
    'borrowerTypeInputId',
    'bridgeUrl' => 'ws://127.0.0.1:8765',
])

<div class="fingerprint-verification mt-3"
     data-rfid-input="{{ $rfidInputId }}"
     data-borrower-id-input="{{ $borrowerIdInputId }}"
     data-borrower-number-input="{{ $borrowerNumberInputId }}"
     data-borrower-type-input="{{ $borrowerTypeInputId }}"
     data-bridge-url="{{ $bridgeUrl }}">
    <div class="alert alert-secondary mb-0 fingerprint-message" aria-live="polite">
        Connecting to the fingerprint scanner...
    </div>
    <input type="hidden" name="fingerprint_id" class="fingerprint-id">
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.fingerprint-verification').forEach(startFingerprintVerification);

    function startFingerprintVerification(panel) {
        const message = panel.querySelector('.fingerprint-message');
        const fingerprintInput = panel.querySelector('.fingerprint-id');
        const rfidInput = document.getElementById(panel.dataset.rfidInput);
        const borrowerIdInput = document.getElementById(panel.dataset.borrowerIdInput);
        const borrowerNumberInput = document.getElementById(panel.dataset.borrowerNumberInput);
        const borrowerTypeInput = document.getElementById(panel.dataset.borrowerTypeInput);
        let socket;
        let reconnectTimer;
        let checking = false;

        function show(text, type) {
            message.className = 'alert alert-' + type + ' mb-0 fingerprint-message';
            message.textContent = text;
        }

        function reset() {
            fingerprintInput.value = '';
            window.dispatchEvent(new CustomEvent('transaction-fingerprint-reset'));
        }

        function connect() {
            clearTimeout(reconnectTimer);
            show('Connecting to the fingerprint scanner...', 'secondary');
            socket = new WebSocket(panel.dataset.bridgeUrl);

            socket.addEventListener('open', function () {
                show('Scanner bridge connected. Scan the matching fingerprint.', 'info');
            });

            socket.addEventListener('message', function (event) {
                let data;

                try {
                    data = JSON.parse(event.data);
                } catch (_) {
                    return;
                }

                if (data.type === 'status') {
                    show(
                        data.connected
                            ? 'Fingerprint scanner ready. Scan the matching fingerprint.'
                            : 'Scanner is reconnecting. Make sure the bridge is running.',
                        data.connected ? 'info' : 'warning'
                    );
                    return;
                }

                if (data.type !== 'serial' || !document.hasFocus()) return;
                handleSerialLine(data.line);
            });

            socket.addEventListener('close', function () {
                show('Scanner bridge unavailable. Reconnecting...', 'warning');
                reconnectTimer = setTimeout(connect, 1500);
            });

            socket.addEventListener('error', function () {
                socket.close();
            });
        }

        async function handleSerialLine(line) {
            if (line === 'FINGERPRINT_NOT_FOUND') {
                reset();
                show('Fingerprint not recognized.', 'danger');
                window.dispatchEvent(new CustomEvent('transaction-fingerprint-rejected'));
                return;
            }

            if (line.startsWith('SCAN_ERROR:') || line.startsWith('SCANNER_ERROR:')) {
                show(line.substring(line.indexOf(':') + 1), 'danger');
                return;
            }

            if (!line.startsWith('FINGERPRINT:') || checking) return;

            const fingerprintId = Number(line.substring('FINGERPRINT:'.length));
            if (!Number.isInteger(fingerprintId) || fingerprintId < 1) return;

            await verifyFingerprint(fingerprintId);
        }

        async function verifyFingerprint(fingerprintId) {
            const currentRfid = rfidInput?.value.trim();
            const currentType = borrowerTypeInput?.value.trim().toLowerCase();

            if (!currentRfid || !currentType) {
                show('Scan the RFID card before the fingerprint.', 'warning');
                return;
            }

            checking = true;
            show('Checking fingerprint...', 'info');

            try {
                const response = await fetch(@json(route('fingerprint.find')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                    },
                    body: JSON.stringify({ fingerprint_id: fingerprintId }),
                });

                const data = await response.json();
                const borrower = data.borrower || {};
                const sameRfid = String(borrower.rfid_tag_uid || '') === currentRfid;
                const sameType = String(borrower.type || '').toLowerCase() === currentType;
                const currentId = borrowerIdInput?.value;
                const sameId = !currentId || String(borrower.id) === String(currentId);
                const currentNumber = borrowerNumberInput?.value;
                const sameNumber = !currentNumber || String(borrower.number) === String(currentNumber);

                if (!response.ok || !data.success || !sameRfid || !sameType || !sameId || !sameNumber) {
                    throw new Error('Fingerprint does not match the scanned RFID card.');
                }

                fingerprintInput.value = fingerprintId;
                show('RFID and fingerprint verified for ' + borrower.name + '.', 'success');
                window.dispatchEvent(new CustomEvent('transaction-fingerprint-verified', {
                    detail: borrower,
                }));
            } catch (error) {
                reset();
                show(error.message || 'Fingerprint verification failed.', 'danger');
                window.dispatchEvent(new CustomEvent('transaction-fingerprint-rejected'));
            } finally {
                checking = false;
            }
        }

        rfidInput?.addEventListener('input', reset);
        connect();
    }
});
</script>
@endonce
