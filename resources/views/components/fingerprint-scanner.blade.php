@props([
    'autoConnect' => false,
    'rfidInputId' => 'rfidInput',
])

<div class="fingerprint-scanner-panel" id="fingerprintScannerPanel">
    <button type="button" id="connectFingerprintScanner" class="btn btn-primary">
        <i class="fa fa-fingerprint mr-1"></i>
        Connect Fingerprint Scanner
    </button>

    <button type="button" id="disconnectFingerprintScanner" class="btn btn-outline-secondary d-none">
        Disconnect
    </button>

    <div id="fingerprintScannerMessage" class="mt-3" aria-live="polite"></div>
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    const connectButton = document.getElementById('connectFingerprintScanner');
    const disconnectButton = document.getElementById('disconnectFingerprintScanner');
    const messageBox = document.getElementById('fingerprintScannerMessage');
    const rfidInputId = @json($rfidInputId);
    const autoConnect = @json((bool) $autoConnect);

    if (!connectButton || !disconnectButton || !messageBox) {
        return;
    }

    let port = null;
    let reader = null;
    let keepReading = false;
    let scanInProgress = false;

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = String(value ?? '');
        return element.innerHTML;
    }

    function showMessage(message, type = 'info') {
        messageBox.innerHTML = `<div class="alert alert-${type} mb-0">${escapeHtml(message)}</div>`;
    }

    function updateButtons(connected) {
        connectButton.classList.toggle('d-none', connected);
        disconnectButton.classList.toggle('d-none', !connected);
    }

    async function identifyFingerprint(fingerprintId) {
        if (scanInProgress) return;
        scanInProgress = true;
        showMessage('Fingerprint detected. Looking up the user...', 'info');

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

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Fingerprint lookup failed.');
            }

            const borrower = data.borrower;
            showMessage(`Fingerprint found: ${borrower.name}`, 'success');

            // Preferred integration: let the current kiosk page decide what to do.
            window.dispatchEvent(new CustomEvent('fingerprint-user-found', {
                detail: borrower,
            }));

            // Compatibility with an existing RFID kiosk lookup.
            const rfidInput = document.getElementById(rfidInputId);
            if (rfidInput && borrower.rfid_tag_uid) {
                rfidInput.value = borrower.rfid_tag_uid;
                rfidInput.dispatchEvent(new Event('input', { bubbles: true }));
                rfidInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        } catch (error) {
            showMessage(error.message, 'danger');
        } finally {
            window.setTimeout(function () {
                scanInProgress = false;
            }, 1000);
        }
    }

    async function handleSerialLine(rawLine) {
        const line = rawLine.trim();
        if (!line) return;

        if (line.startsWith('FINGERPRINT:')) {
            const fingerprintId = Number(line.substring('FINGERPRINT:'.length));
            if (Number.isInteger(fingerprintId) && fingerprintId > 0) {
                await identifyFingerprint(fingerprintId);
            }
            return;
        }

        if (line === 'FINGERPRINT_NOT_FOUND') {
            showMessage('Fingerprint not recognized.', 'warning');
            return;
        }

        if (line.startsWith('SCANNER_ERROR:') || line.startsWith('SCAN_ERROR:')) {
            showMessage(line.substring(line.indexOf(':') + 1), 'danger');
            return;
        }

        if (line.startsWith('SCANNER_READY:')) {
            showMessage('Fingerprint scanner is ready.', 'success');
        }
    }

    async function readPort() {
        const textDecoder = new TextDecoder();
        let buffer = '';
        keepReading = true;

        while (port && port.readable && keepReading) {
            reader = port.readable.getReader();
            try {
                while (keepReading) {
                    const { value, done } = await reader.read();
                    if (done) break;

                    buffer += textDecoder.decode(value, { stream: true });
                    const lines = buffer.split(/\r?\n/);
                    buffer = lines.pop() || '';

                    for (const line of lines) {
                        await handleSerialLine(line);
                    }
                }
            } finally {
                reader.releaseLock();
                reader = null;
            }
        }
    }

    async function connectScanner(requestNewPort = true) {
        if (!('serial' in navigator)) {
            showMessage('Use Chrome or Edge on localhost or HTTPS. Web Serial is not available in this browser.', 'danger');
            return;
        }

        try {
            if (requestNewPort) {
                port = await navigator.serial.requestPort();
            } else {
                const ports = await navigator.serial.getPorts();
                port = ports[0] || null;
                if (!port) return;
            }

            await port.open({ baudRate: 9600 });
            updateButtons(true);
            showMessage('Scanner connected. Place a finger on the sensor.', 'success');
            await readPort();
        } catch (error) {
            updateButtons(false);
            showMessage(`Unable to connect: ${error.message}`, 'danger');
        }
    }

    async function disconnectScanner() {
        keepReading = false;
        try {
            if (reader) await reader.cancel();
            if (port) await port.close();
        } catch (error) {
            console.error(error);
        } finally {
            port = null;
            updateButtons(false);
            showMessage('Fingerprint scanner disconnected.', 'secondary');
        }
    }

    function releaseScannerOnPageExit() {
        keepReading = false;

        if (reader) {
            reader.cancel().catch(function () {});
        }

        if (port) {
            port.close().catch(function () {});
        }
    }

    connectButton.addEventListener('click', () => connectScanner(true));
    disconnectButton.addEventListener('click', disconnectScanner);

    navigator.serial?.addEventListener('disconnect', function () {
        port = null;
        keepReading = false;
        updateButtons(false);
        showMessage('Fingerprint scanner was disconnected.', 'warning');
    });

    window.addEventListener('pagehide', releaseScannerOnPageExit);
    window.addEventListener('beforeunload', releaseScannerOnPageExit);

    if (autoConnect) connectScanner(false);
});
</script>
@endonce
