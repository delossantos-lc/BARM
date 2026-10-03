@include('layouts.header')
@include('layouts.css')

<style>
    :root {
        --mgmt-primary: #d6538c;
        --mgmt-primary-dark: #bd3f77;
        --mgmt-primary-soft: #fff2f7;
        --mgmt-primary-softer: #fff8fb;
        --mgmt-page: #f7f7fb;
        --mgmt-surface: #ffffff;
        --mgmt-border: #ebe7ed;
        --mgmt-border-soft: #f2eff3;
        --mgmt-text: #292631;
        --mgmt-muted: #797482;
        --mgmt-success: #218143;
        --mgmt-success-soft: #eaf8ef;
        --mgmt-danger: #bd3434;
        --mgmt-danger-soft: #ffeded;
        --mgmt-info: #315caa;
        --mgmt-info-soft: #eef3ff;
        --mgmt-warning: #b57b16;
        --mgmt-warning-soft: #fff6e5;
        --mgmt-shadow: 0 10px 30px rgba(42, 35, 48, .06);
        --mgmt-shadow-hover: 0 14px 35px rgba(42, 35, 48, .09);
    }

    .mgmt-page { color: var(--mgmt-text); }

    /* ═══════════════════════════════════════
       PAGE TITLES
       ═══════════════════════════════════════ */
    .mgmt-page .page-titles {
        align-items: center;
        margin-bottom: 24px !important;
        padding: 22px 24px;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: linear-gradient(115deg, #fff 0%, #fff8fb 100%);
        box-shadow: var(--mgmt-shadow);
    }
    .mgmt-page .welcome-text h4 {
        margin-bottom: 5px;
        color: var(--mgmt-text);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.35px;
    }
    .mgmt-page .welcome-text span { color: var(--mgmt-muted); font-size: 13px; }
    .mgmt-page .breadcrumb {
        margin: 0;
        padding: 9px 13px;
        border-radius: 10px;
        background: rgba(255,255,255,.78);
    }
    .mgmt-page .breadcrumb-item a { color: var(--mgmt-primary); font-weight: 600; }

    /* ═══════════════════════════════════════
       CARDS
       ═══════════════════════════════════════ */
    .restore-card {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: var(--mgmt-surface);
        box-shadow: var(--mgmt-shadow);
    }

    .restore-card .card-body {
        padding: 28px 30px;
    }

    /* ═══════════════════════════════════════
       RESTORE ICON
       ═══════════════════════════════════════ */
    .restore-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 58px;
        height: 58px;
        border-radius: 14px;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary);
        font-size: 25px;
        flex-shrink: 0;
        box-shadow: 0 6px 14px rgba(213, 91, 145, .18);
    }

    /* ═══════════════════════════════════════
       DANGER PANEL
       ═══════════════════════════════════════ */
    .danger-panel {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px 18px;
        border: 1px solid #f5d6e1;
        border-radius: 14px;
        background: var(--mgmt-primary-softer);
        color: var(--mgmt-primary-dark);
        font-size: 13px;
        line-height: 1.55;
    }
    .danger-panel i {
        margin-top: 2px;
        color: var(--mgmt-danger);
        font-size: 16px;
        flex-shrink: 0;
    }
    .danger-panel strong {
        color: var(--mgmt-danger);
        font-weight: 700;
    }

    /* ═══════════════════════════════════════
       FORM CONTROLS
       ═══════════════════════════════════════ */
    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #57515e;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .25px;
        text-transform: uppercase;
    }

    .form-control {
        width: 100%;
        min-height: 46px;
        padding: 10px 14px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background-color: #fff;
        color: var(--mgmt-text);
        font-size: 13px;
        height: auto;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        appearance: none;
        -webkit-appearance: none;
    }
    .form-control:hover { border-color: #c9c2ce; }
    .form-control:focus {
        border-color: var(--mgmt-primary);
        background-color: var(--mgmt-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .12);
        outline: none;
    }

    select.form-control {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23797482' d='M6 8.825L1.175 4 2.238 2.938 6 6.7l3.763-3.763L10.825 4z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 38px;
    }

    .form-control::placeholder {
        color: #b0aab5;
        font-weight: 400;
    }

    input[type="file"].form-control {
        padding: 8px 14px;
    }
    input[type="file"].form-control::file-selector-button {
        margin-right: 12px;
        padding: 6px 14px;
        border: 0;
        border-radius: 7px;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s ease;
    }
    input[type="file"].form-control::file-selector-button:hover {
        background: var(--mgmt-primary);
        color: #fff;
    }

    .form-text {
        display: block;
        margin-top: 6px;
        color: var(--mgmt-muted);
        font-size: 12px;
    }

    /* ═══════════════════════════════════════
       BUTTONS
       ═══════════════════════════════════════ */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 46px;
        padding: 10px 22px;
        border-radius: 11px;
        font-size: 13px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }
    .btn:hover { transform: translateY(-1px); }

    .btn-danger {
        border: 0;
        background: var(--mgmt-danger);
        color: #fff;
        box-shadow: 0 6px 16px rgba(189, 52, 52, .28);
    }
    .btn-danger:hover,
    .btn-danger:focus {
        background: #a32c2c;
        color: #fff;
        box-shadow: 0 8px 20px rgba(189, 52, 52, .36);
    }

    .action-button {
        border-radius: 11px;
        font-weight: 700;
    }

    /* ═══════════════════════════════════════
       ALERTS
       ═══════════════════════════════════════ */
    .alert {
        border-radius: 14px;
        border: 1px solid var(--mgmt-border);
        font-size: 13px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }
    .alert-success {
        border-color: #c9e9d4;
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }
    .alert-danger {
        border-color: #f3c9c9;
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
    }
    .alert .close {
        color: inherit;
        opacity: .6;
        font-size: 18px;
    }
    .alert .close:hover { opacity: 1; }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 767px) {
        .mgmt-page .page-titles { padding: 18px; }
        .restore-card .card-body { padding: 20px 18px; }
        .danger-panel { flex-direction: column; gap: 8px; }
        .form-control { min-height: 44px; }
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

<div class="content-body mgmt-page">
    <div class="container-fluid py-4">

        {{-- PAGE TITLE --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Restore Database</h4>
                    <span>Recover the BARM database from an SQL backup</span>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Restore Database</li>
                </ol>
            </div>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa fa-exclamation-circle mr-2"></i>
                <strong>Please check the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- RESTORE CARD --}}
        <div class="card restore-card">
            <div class="card-body">

                {{-- HEADER --}}
                <div class="d-flex align-items-center mb-4">
                    <div class="restore-icon mr-3">
                        <i class="fa fa-history"></i>
                    </div>
                    <div>
                        <h4 class="mb-1" style="color: var(--mgmt-text); font-size: 17px; font-weight: 700;">
                            Select a Restore Source
                        </h4>
                        <span class="text-muted" style="font-size: 13px;">
                            Use a stored backup or upload an SQL backup file.
                        </span>
                    </div>
                </div>

                {{-- DANGER PANEL --}}
                <div class="danger-panel mb-4">
                    <i class="fa fa-warning"></i>
                    <div>
                        <strong>Important warning</strong>
                        <p class="mb-0 mt-1" style="color: var(--mgmt-muted); font-size: 13px;">
                            Restoring replaces current records. A safety backup is automatically created before restoration begins.
                        </p>
                    </div>
                </div>

                {{-- FORM --}}
                <form method="POST" action="{{ route('database.restore.run') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- RESTORE SOURCE --}}
                    <div class="form-group">
                        <label for="restoreSource">Restore Source</label>
                        <select name="restore_source" id="restoreSource" class="form-control" required>
                            <option value="saved" {{ old('restore_source') === 'upload' ? '' : 'selected' }}>Saved Backup</option>
                            <option value="upload" {{ old('restore_source') === 'upload' ? 'selected' : '' }}>Upload SQL File</option>
                        </select>
                    </div>

                    {{-- SAVED BACKUP --}}
                    <div class="form-group" id="savedBackupGroup">
                        <label for="backupFile">Saved Backup</label>
                        <select name="backup_file" id="backupFile" class="form-control">
                            <option value="">Select a backup</option>
                            @foreach($backups as $backup)
                                <option value="{{ $backup['name'] }}" {{ old('backup_file') === $backup['name'] ? 'selected' : '' }}>
                                    {{ $backup['name'] }} - {{ $backup['created_at'] }} - {{ $backup['size'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- UPLOAD BACKUP --}}
                    <div class="form-group d-none" id="uploadBackupGroup">
                        <label for="sqlFile">Upload SQL Backup</label>
                        <input type="file" name="sql_file" id="sqlFile" class="form-control" accept=".sql,.txt">
                        <small class="form-text">Only SQL or text backup files are accepted.</small>
                    </div>

                    {{-- PASSWORD --}}
                    <div class="form-group">
                        <label for="currentPassword">Current Admin Password</label>
                        <input type="password" name="current_password" id="currentPassword" class="form-control" autocomplete="current-password" required>
                    </div>

                    {{-- CONFIRMATION --}}
                    <div class="form-group">
                        <label for="restoreConfirmation">Type RESTORE to confirm</label>
                        <input type="text" name="restore_confirmation" id="restoreConfirmation" class="form-control" placeholder="RESTORE" autocomplete="off" required>
                    </div>

                    {{-- SUBMIT --}}
                    <button type="submit" class="btn btn-danger action-button px-4" onclick="return confirm('Restore the database now? A safety backup will be created first.');">
                        <i class="fa fa-refresh"></i>
                        Restore Database
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const source = document.getElementById('restoreSource');
    const saved = document.getElementById('savedBackupGroup');
    const upload = document.getElementById('uploadBackupGroup');

    function updateSource() {
        const isUpload = source.value === 'upload';
        saved.classList.toggle('d-none', isUpload);
        upload.classList.toggle('d-none', !isUpload);
        saved.querySelector('select').required = !isUpload;
        upload.querySelector('input').required = isUpload;
    }

    source.addEventListener('change', updateSource);
    updateSource();
});
</script>