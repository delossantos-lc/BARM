@props([
    'inputId',
    'label' => 'Fingerprint',
    'value' => null,
    'bridgeUrl' => 'ws://127.0.0.1:8765',
])

@php
    $usedFingerprintIds = \App\Models\Student::query()
        ->whereNotNull('fingerprint_id')
        ->pluck('fingerprint_id')
        ->merge(
            \App\Models\Personnel::query()
                ->whereNotNull('fingerprint_id')
                ->pluck('fingerprint_id')
        )
        ->map(fn ($id) => (int) $id)
        ->all();

    $nextFingerprintId = collect(range(1, 127))
        ->first(fn ($id) => !in_array($id, $usedFingerprintIds, true));
@endphp

<div class="col-md-12">
    <div class="form-group">
        <label>{{ $label }} <span class="text-danger">*</span></label>

        <input
            type="hidden"
            name="fingerprint_id"
            id="{{ $inputId }}"
            value="{{ old('fingerprint_id', $value) }}"
            required
        >

        <div class="border rounded p-3 bg-light fingerprint-form-box"
             data-input-id="{{ $inputId }}"
             data-next-id="{{ $nextFingerprintId }}"
             data-bridge-url="{{ $bridgeUrl }}">
            <div class="d-flex align-items-center flex-wrap">
                <button type="button" class="btn btn-primary fingerprint-enroll" disabled>
                    <i class="fa fa-fingerprint mr-1"></i>
                    Enroll Fingerprint
                </button>

                <span class="fingerprint-connection text-muted ml-3">
                    Connecting to scanner...
                </span>
            </div>

            <div class="fingerprint-status mt-3" aria-live="polite">
                <span class="text-muted">The scanner connects automatically.</span>
            </div>
        </div>
    </div>
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    const boxes = Array.from(document.querySelectorAll('.fingerprint-form-box'));
    if (!boxes.length) return;

    const bridgeUrl = boxes[0].dataset.bridgeUrl;
    let socket = null;
    let activeBox = null;
    let reconnectTimer = null;

    function setStatus(box, text, type) {
        const status = box.querySelector('.fingerprint-status');
        status.innerHTML = '';

        const alert = document.createElement('div');
        alert.className = 'alert alert-' + type + ' mb-0 py-2';
        alert.textContent = text;
        status.appendChild(alert);
    }

    function setConnected(connected) {
        boxes.forEach(function (box) {
            const button = box.querySelector('.fingerprint-enroll');
            const label = box.querySelector('.fingerprint-connection');
            const hasSlot = Number(box.dataset.nextId) > 0;

            button.disabled = !connected || !hasSlot;
            label.textContent = connected ? 'Scanner ready' : 'Scanner reconnecting...';
            label.className = 'fingerprint-connection ml-3 ' +
                (connected ? 'text-success' : 'text-muted');

            if (connected && !hasSlot) {
                setStatus(box, 'The fingerprint sensor has no available slots.', 'danger');
            }
        });
    }

    function connect() {
        clearTimeout(reconnectTimer);
        socket = new WebSocket(bridgeUrl);

        socket.addEventListener('open', function () {
            setConnected(true);
        });

        socket.addEventListener('message', function (event) {
            let data;

            try {
                data = JSON.parse(event.data);
            } catch (_) {
                return;
            }

            if (data.type === 'status') {
                setConnected(Boolean(data.connected));
                return;
            }

            if (data.type === 'command-error' && activeBox) {
                setStatus(activeBox, data.message, 'danger');
                activeBox.querySelector('.fingerprint-enroll').disabled = false;
                activeBox = null;
                return;
            }

            if (data.type !== 'serial' || !activeBox) return;
            handleScannerLine(data.line);
        });

        socket.addEventListener('close', function () {
            setConnected(false);
            reconnectTimer = setTimeout(connect, 1500);
        });

        socket.addEventListener('error', function () {
            socket.close();
        });
    }

    function sendCommand(command) {
        if (!socket || socket.readyState !== WebSocket.OPEN) {
            throw new Error('The fingerprint scanner is reconnecting. Please wait.');
        }

        socket.send(JSON.stringify({ type: 'command', command: command }));
    }

    function handleScannerLine(rawLine) {
        const line = String(rawLine).trim();

        if (line.startsWith('ENROLL_PROMPT:')) {
            setStatus(activeBox, line.substring('ENROLL_PROMPT:'.length), 'info');
            return;
        }

        if (line.startsWith('ENROLL_ERROR:')) {
            setStatus(activeBox, line.substring('ENROLL_ERROR:'.length), 'danger');
            activeBox.querySelector('.fingerprint-enroll').disabled = false;
            activeBox = null;
            return;
        }

        if (!line.startsWith('ENROLL_SUCCESS:')) return;

        const fingerprintId = Number(line.substring('ENROLL_SUCCESS:'.length));
        const input = document.getElementById(activeBox.dataset.inputId);

        input.value = fingerprintId;
        setStatus(
            activeBox,
            'Fingerprint enrolled. You can now save the Student or Personnel record.',
            'success'
        );
        activeBox.querySelector('.fingerprint-enroll').disabled = true;
        activeBox = null;
    }

    boxes.forEach(function (box) {
        const button = box.querySelector('.fingerprint-enroll');
        const input = document.getElementById(box.dataset.inputId);

        if (input.value) {
            button.disabled = true;
            setStatus(box, 'Fingerprint is already enrolled.', 'success');
        }

        button.addEventListener('click', function () {
            if (activeBox) {
                setStatus(box, 'Another fingerprint enrollment is still running.', 'warning');
                return;
            }

            const fingerprintId = Number(box.dataset.nextId);

            if (!fingerprintId) {
                setStatus(box, 'No fingerprint slot is available.', 'danger');
                return;
            }

            try {
                activeBox = box;
                button.disabled = true;
                input.value = '';
                setStatus(box, 'Place the finger on the sensor.', 'info');
                sendCommand('ENROLL:' + fingerprintId);
            } catch (error) {
                activeBox = null;
                button.disabled = false;
                setStatus(box, error.message, 'warning');
            }
        });
    });

    connect();
});
</script>
@endonce
