@props([
    'students',
    'personnel',
])

<div class="card management-card mb-4" id="fingerprintEnrollmentCard">
    <div class="card-header">
        <div>
            <h5 class="mb-1">
                <i class="fa fa-fingerprint mr-2 text-primary"></i>
                Fingerprint Enrollment
            </h5>
            <small class="text-muted">
                Enroll and assign a fingerprint to a Student or Personnel record.
            </small>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="fingerprintPersonType">Person Type</label>
                <select id="fingerprintPersonType" class="form-control">
                    <option value="student">Student</option>
                    <option value="personnel">Personnel</option>
                </select>
            </div>

            <div class="col-md-5 form-group">
                <label for="fingerprintPersonId">Student or Personnel</label>
                <select id="fingerprintPersonId" class="form-control">
                    <option value="">Select a record</option>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="fingerprintTemplateId">Sensor Slot</label>
                <input
                    type="number"
                    id="fingerprintTemplateId"
                    class="form-control"
                    min="1"
                    max="127"
                    placeholder="1-127"
                >
            </div>

            <div class="col-md-2 form-group d-flex align-items-end">
                <button type="button" id="connectEnrollmentScanner" class="btn btn-outline-primary btn-block">
                    Connect Scanner
                </button>
            </div>
        </div>

        <div class="d-flex flex-wrap">
            <button type="button" id="startFingerprintEnrollment" class="btn btn-primary mr-2 mb-2" disabled>
                <i class="fa fa-fingerprint mr-1"></i>
                Enroll Fingerprint
            </button>

            <button type="button" id="deleteFingerprintTemplate" class="btn btn-outline-danger mb-2" disabled>
                Delete Sensor Template
            </button>
        </div>

        <div id="fingerprintEnrollmentMessage" class="mt-3" aria-live="polite"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const students = @json($students->map(fn ($student) => [
        'id' => $student->id,
        'name' => trim($student->firstname . ' ' . $student->lastname),
        'number' => $student->student_number,
        'fingerprint_id' => $student->fingerprint_id,
    ])->values());

    const personnel = @json($personnel->map(fn ($person) => [
        'id' => $person->id,
        'name' => trim($person->firstname . ' ' . $person->lastname),
        'number' => $person->employee_number,
        'fingerprint_id' => $person->fingerprint_id,
    ])->values());

    const typeInput = document.getElementById('fingerprintPersonType');
    const personInput = document.getElementById('fingerprintPersonId');
    const templateInput = document.getElementById('fingerprintTemplateId');
    const connectButton = document.getElementById('connectEnrollmentScanner');
    const enrollButton = document.getElementById('startFingerprintEnrollment');
    const deleteButton = document.getElementById('deleteFingerprintTemplate');
    const messageBox = document.getElementById('fingerprintEnrollmentMessage');

    let port = null;
    let reader = null;
    let writer = null;
    let keepReading = false;
    let pendingEnrollment = null;

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = String(value ?? '');
        return element.innerHTML;
    }

    function showMessage(message, type = 'info') {
        messageBox.innerHTML = `<div class="alert alert-${type} mb-0">${escapeHtml(message)}</div>`;
    }

    function populatePeople() {
        const records = typeInput.value === 'student' ? students : personnel;
        personInput.innerHTML = '<option value="">Select a record</option>';

        records.forEach(function (record) {
            const option = document.createElement('option');
            option.value = record.id;
            option.dataset.fingerprintId = record.fingerprint_id || '';
            option.textContent = `${record.name} (${record.number})${record.fingerprint_id ? ' - Fingerprint #' + record.fingerprint_id : ''}`;
            personInput.appendChild(option);
        });

        templateInput.value = '';
    }

    function updateSelectedPerson() {
        const option = personInput.options[personInput.selectedIndex];
        const existingId = option?.dataset?.fingerprintId || '';
        templateInput.value = existingId;
        templateInput.readOnly = existingId !== '';

        if (existingId) {
            showMessage(
                `This record already uses fingerprint slot #${existingId}. Re-enrollment will update that same slot.`,
                'info'
            );
        }
    }

    async function sendCommand(command) {
        if (!writer) throw new Error('Connect the fingerprint scanner first.');
        await writer.write(new TextEncoder().encode(command + '\n'));
    }

    async function saveAssignment(templateId) {
        const response = await fetch(@json(route('fingerprint.assign')), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
            },
            body: JSON.stringify({
                person_type: pendingEnrollment.personType,
                person_id: pendingEnrollment.personId,
                fingerprint_id: templateId,
            }),
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            // Avoid leaving an orphaned sensor template when database saving fails.
            await sendCommand(`DELETE:${templateId}`);
            throw new Error(data.message || 'The fingerprint was scanned but could not be assigned.');
        }

        showMessage('Fingerprint enrolled and assigned successfully.', 'success');
        window.setTimeout(() => window.location.reload(), 1200);
    }

    async function handleLine(rawLine) {
        const line = rawLine.trim();
        if (!line) return;

        if (line.startsWith('ENROLL_PROMPT:')) {
            showMessage(line.substring('ENROLL_PROMPT:'.length), 'info');
            return;
        }

        if (line.startsWith('ENROLL_ERROR:')) {
            pendingEnrollment = null;
            enrollButton.disabled = false;
            showMessage(line.substring('ENROLL_ERROR:'.length), 'danger');
            return;
        }

        if (line.startsWith('ENROLL_SUCCESS:')) {
            const templateId = Number(line.substring('ENROLL_SUCCESS:'.length));
            try {
                await saveAssignment(templateId);
            } catch (error) {
                pendingEnrollment = null;
                enrollButton.disabled = false;
                showMessage(error.message, 'danger');
            }
            return;
        }

        if (line.startsWith('DELETE_SUCCESS:')) {
            showMessage('Fingerprint template deleted from the sensor.', 'success');
            return;
        }

        if (line.startsWith('DELETE_ERROR:') || line.startsWith('SCANNER_ERROR:')) {
            showMessage(line.substring(line.indexOf(':') + 1), 'danger');
        }
    }

    async function readPort() {
        const decoder = new TextDecoder();
        let buffer = '';
        keepReading = true;

        while (port?.readable && keepReading) {
            reader = port.readable.getReader();
            try {
                while (keepReading) {
                    const { value, done } = await reader.read();
                    if (done) break;
                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split(/\r?\n/);
                    buffer = lines.pop() || '';
                    for (const line of lines) await handleLine(line);
                }
            } finally {
                reader.releaseLock();
                reader = null;
            }
        }
    }

    connectButton.addEventListener('click', async function () {
        if (!('serial' in navigator)) {
            showMessage('Use Chrome or Edge on localhost or HTTPS.', 'danger');
            return;
        }

        try {
            port = await navigator.serial.requestPort();
            await port.open({ baudRate: 9600 });
            writer = port.writable.getWriter();
            connectButton.disabled = true;
            connectButton.textContent = 'Scanner Connected';
            enrollButton.disabled = false;
            deleteButton.disabled = false;
            showMessage('Scanner connected. Select a person and an unused slot.', 'success');
            readPort();
        } catch (error) {
            showMessage(`Unable to connect: ${error.message}`, 'danger');
        }
    });

    enrollButton.addEventListener('click', async function () {
        const personId = Number(personInput.value);
        const templateId = Number(templateInput.value);

        if (!personId) {
            showMessage('Select a Student or Personnel record.', 'warning');
            return;
        }

        if (!Number.isInteger(templateId) || templateId < 1 || templateId > 127) {
            showMessage('Sensor slot must be between 1 and 127.', 'warning');
            return;
        }

        pendingEnrollment = {
            personType: typeInput.value,
            personId: personId,
            templateId: templateId,
        };

        enrollButton.disabled = true;
        showMessage('Starting enrollment...', 'info');
        await sendCommand(`ENROLL:${templateId}`);
    });

    deleteButton.addEventListener('click', async function () {
        const templateId = Number(templateInput.value);
        if (!Number.isInteger(templateId) || templateId < 1 || templateId > 127) {
            showMessage('Enter a valid sensor slot from 1 to 127.', 'warning');
            return;
        }

        if (!window.confirm(`Delete fingerprint template #${templateId} from the sensor?`)) return;
        await sendCommand(`DELETE:${templateId}`);
    });

    typeInput.addEventListener('change', populatePeople);
    personInput.addEventListener('change', updateSelectedPerson);
    populatePeople();
});
</script>
