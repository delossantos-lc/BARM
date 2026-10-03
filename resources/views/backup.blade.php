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

    .summary-card,
    .backup-card {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: var(--mgmt-surface);
        box-shadow: var(--mgmt-shadow);
    }

    .summary-card {
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--mgmt-shadow-hover);
    }
    .summary-card .card-body { padding: 22px 24px; }

    .backup-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 58px;
        height: 58px;
        border-radius: 14px;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary);
        font-size: 24px;
        flex-shrink: 0;
        box-shadow: 0 6px 14px rgba(213, 91, 145, .18);
    }
    .backup-icon.icon-database {
        background: var(--mgmt-info-soft);
        color: var(--mgmt-info);
        box-shadow: 0 6px 14px rgba(49, 92, 170, .18);
    }

    .summary-card h6 {
        margin-bottom: 4px;
        color: var(--mgmt-muted);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .35px;
        text-transform: uppercase;
    }
    .summary-number {
        margin: 0;
        color: var(--mgmt-text);
        font-size: 31px;
        font-weight: 800;
        line-height: 1;
    }

    .create-backup-card .card-body { padding: 22px 24px; }
    .create-backup-card h4 {
        margin-bottom: 4px;
        color: var(--mgmt-text);
        font-size: 17px;
        font-weight: 700;
    }
    .create-backup-card .text-muted { font-size: 13px; }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 10px 18px;
        border-radius: 11px;
        font-size: 13px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }
    .btn:hover { transform: translateY(-1px); }

    .btn-primary {
        border: 0;
        background: var(--mgmt-primary);
        color: #fff;
    }
    .btn-primary:hover,
    .btn-primary:focus {
        background: var(--mgmt-primary-dark);
        color: #fff;
        box-shadow: 0 4px 12px rgba(213, 91, 145, .3);
    }

    .btn-secondary {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }
    .btn-secondary:hover,
    .btn-secondary:focus {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .12);
    }

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
        min-height: 34px;
        padding: 6px 12px;
        border: 0;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }
    .action-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(42, 35, 48, .12);
    }

    .btn-info.action-button {
        background: var(--mgmt-info-soft);
        color: var(--mgmt-info);
    }
    .btn-info.action-button:hover {
        background: var(--mgmt-info);
        color: #fff;
    }

    .btn-danger.action-button {
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
    }
    .btn-danger.action-button:hover {
        background: var(--mgmt-danger);
        color: #fff;
    }

    .backup-card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        min-height: 78px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--mgmt-border);
        background: #fff;
    }
    .backup-card .card-title {
        color: var(--mgmt-text);
        font-size: 17px;
        font-weight: 700;
    }
    .backup-card .card-title i { color: var(--mgmt-primary); }
    .backup-card .card-body { padding: 22px; }

    .backup-table { width: 100%; margin-bottom: 0; }
    .backup-table thead th {
        padding: 15px 14px;
        border: 0;
        border-bottom: 1px solid var(--mgmt-border);
        background: #f8f7fa;
        color: #625c68;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .3px;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .backup-table tbody td {
        padding: 14px;
        border-top: 1px solid #f0edf1;
        background: #fff;
        vertical-align: middle;
    }
    .backup-table.table-hover tbody tr:hover td { background: #fff7fa; }
    .backup-table td strong {
        color: var(--mgmt-text);
        font-weight: 700;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 12.5px;
    }

    .modal-content {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        box-shadow: var(--mgmt-shadow-hover);
    }
    .modal-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }
    .modal-title { color: var(--mgmt-text); font-size: 16px; font-weight: 700; }
    .modal-title i { color: var(--mgmt-primary); }
    .modal-body { padding: 22px; background: #fff; }
    .modal-footer {
        padding: 16px 22px;
        border-top: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }

    .modal label {
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
        min-height: 44px;
        padding: 10px 14px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background-color: #fff;
        color: var(--mgmt-text);
        font-size: 13px;
        height: auto;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }
    .form-control:hover { border-color: #c9c2ce; }
    .form-control:focus {
        border-color: var(--mgmt-primary);
        background-color: var(--mgmt-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .12);
        outline: none;
    }

    .delete-backup-modal .modal-dialog { max-width: 460px; }
    .delete-backup-modal .modal-content {
        border-radius: 18px;
        box-shadow: 0 25px 60px rgba(42, 35, 48, .22);
        animation: deleteBackupIn .22s ease;
    }
    @keyframes deleteBackupIn {
        from { opacity: 0; transform: translateY(-12px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .delete-backup-modal .delete-icon-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 78px;
        height: 78px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffe3ec 0%, #ffd0de 100%);
        color: var(--mgmt-danger);
        font-size: 30px;
        box-shadow: 0 10px 24px rgba(189, 52, 52, .18);
    }
    .delete-backup-modal .delete-icon-wrap::before,
    .delete-backup-modal .delete-icon-wrap::after {
        position: absolute;
        border-radius: 50%;
        content: "";
    }
    .delete-backup-modal .delete-icon-wrap::before {
        inset: -8px;
        border: 1px dashed rgba(189, 52, 52, .25);
        animation: deletePulse 2.4s linear infinite;
    }
    .delete-backup-modal .delete-icon-wrap::after {
        inset: -16px;
        border: 1px solid rgba(189, 52, 52, .12);
    }
    @keyframes deletePulse {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }

    .delete-backup-modal .delete-modal-body {
        padding: 32px 30px 24px;
        text-align: center;
        background: #fff;
    }
    .delete-backup-modal .delete-title {
        margin-bottom: 8px;
        color: var(--mgmt-text);
        font-size: 19px;
        font-weight: 800;
        letter-spacing: -.3px;
    }
    .delete-backup-modal .delete-message {
        margin: 0 auto 20px;
        max-width: 330px;
        color: var(--mgmt-muted);
        font-size: 13.5px;
        line-height: 1.55;
    }
    .delete-backup-modal .delete-message strong {
        color: var(--mgmt-text);
        font-weight: 700;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 12.5px;
    }
    .delete-backup-modal .delete-warning {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 18px;
        padding: 12px 14px;
        border: 1px solid #f5d6e1;
        border-radius: 12px;
        background: var(--mgmt-primary-softer);
        color: var(--mgmt-primary-dark);
        font-size: 12px;
        font-weight: 600;
        text-align: left;
        line-height: 1.5;
    }
    .delete-backup-modal .delete-warning i {
        margin-top: 2px;
        color: var(--mgmt-primary);
        font-size: 13px;
    }
    .delete-backup-modal .confirm-label {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
        color: #57515e;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .25px;
        text-transform: uppercase;
        text-align: left;
    }
    .delete-backup-modal .confirm-label i {
        color: var(--mgmt-primary);
        font-size: 11px;
    }
    .delete-backup-modal .form-control {
        text-align: center;
        font-weight: 700;
        letter-spacing: 2px;
        font-size: 14px;
    }
    .delete-backup-modal .modal-footer {
        display: flex;
        gap: 10px;
        padding: 18px 24px;
        border-top: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }
    .delete-backup-modal .modal-footer .btn {
        flex: 1;
        min-height: 46px;
    }

    .alert { border-radius: 14px; border: 1px solid var(--mgmt-border); font-size: 13px; }
    .alert-success { border-color: #c9e9d4; background: var(--mgmt-success-soft); color: var(--mgmt-success); }
    .alert-danger { border-color: #f3c9c9; background: var(--mgmt-danger-soft); color: var(--mgmt-danger); }

    .backup-table tbody td[colspan="5"] {
        padding: 55px 20px !important;
        text-align: center;
        color: var(--mgmt-muted);
        font-size: 13px;
    }
    .backup-table tbody td[colspan="5"] i {
        display: block;
        color: #ccc;
        font-size: 45px;
        margin-bottom: 12px;
    }

    @media (max-width: 767px) {
        .mgmt-page .page-titles { padding: 18px; }
        .backup-card .card-header { align-items: stretch; flex-direction: column; }
        .backup-card .card-body { padding: 16px; }
        .create-backup-card .card-body { padding: 18px; }
        .create-backup-card .d-flex { flex-direction: column; align-items: stretch !important; gap: 14px; }
        .create-backup-card form { width: 100%; }
        .create-backup-card form .btn { width: 100%; }
        .summary-number { font-size: 26px; }
        .action-button { min-width: 40px; }
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
                    <h4>Database Backup</h4>
                    <span>Create and manage BARM database backups</span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Database Backup</li>
                </ol>
            </div>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
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
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- SUMMARY CARD --}}
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6">
                <div class="card summary-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="backup-icon mr-3">
                            <i class="fa fa-archive"></i>
                        </div>
                        <div>
                            <h6>Stored Backups</h6>
                            <p class="summary-number">{{ count($backups) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CREATE BACKUP CARD --}}
        <div class="card backup-card create-backup-card mb-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="backup-icon icon-database mr-3">
                        <i class="fa fa-database"></i>
                    </div>
                    <div>
                        <h4 class="mb-1">Create New Backup</h4>
                        <span class="text-muted">Export every table and record into an SQL file.</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('database.backup.create') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-cloud-download"></i>
                        Create Backup
                    </button>
                </form>
            </div>
        </div>

        {{-- BACKUP HISTORY CARD --}}
        <div class="card backup-card">
            <div class="card-header">
                <div>
                    <h4 class="card-title mb-1">
                        <i class="fa fa-history mr-2"></i>
                        Backup History
                    </h4>
                    <small class="text-muted">Newest backups appear first.</small>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover backup-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Filename</th>
                                <th>Created</th>
                                <th>Size</th>
                                <th style="min-width:210px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($backups as $backup)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <strong>{{ $backup['name'] }}</strong>
                                    </td>

                                    <td>{{ $backup['created_at'] }}</td>

                                    <td>{{ $backup['size'] }}</td>

                                    <td>
                                        <a
                                            class="btn btn-info action-button"
                                            href="{{ route('database.backup.download', $backup['name']) }}"
                                        >
                                            <i class="fa fa-download"></i>
                                            Download
                                        </a>

                                        <button
                                            class="btn btn-danger action-button"
                                            type="button"
                                            data-toggle="modal"
                                            data-target="#deleteBackup{{ $loop->index }}"
                                        >
                                            <i class="fa fa-trash"></i>
                                        </button>

                                        {{-- DELETE CONFIRMATION MODAL --}}
                                        <div class="modal fade delete-backup-modal"
                                             id="deleteBackup{{ $loop->index }}"
                                             tabindex="-1"
                                             role="dialog"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                          action="{{ route('database.backup.delete', $backup['name']) }}">
                                                        @csrf
                                                        @method('DELETE')

                                                        <div class="delete-modal-body">
                                                            <div class="delete-icon-wrap">
                                                                <i class="fa fa-trash-o"></i>
                                                            </div>

                                                            <h5 class="delete-title">Delete Backup?</h5>

                                                            <p class="delete-message">
                                                                You are about to permanently delete
                                                                <strong>{{ $backup['name'] }}</strong>.
                                                                This action cannot be undone.
                                                            </p>

                                                            <div class="delete-warning">
                                                                <i class="fa fa-exclamation-triangle"></i>
                                                                <span>
                                                                    The backup file will be removed from the server.
                                                                    Type <strong>DELETE</strong> below to confirm.
                                                                </span>
                                                            </div>

                                                            <label class="confirm-label"
                                                                   for="deleteConfirmation{{ $loop->index }}">
                                                                <i class="fa fa-keyboard-o"></i>
                                                                Confirmation
                                                            </label>

                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="deleteConfirmation{{ $loop->index }}"
                                                                name="delete_confirmation"
                                                                placeholder="DELETE"
                                                                autocomplete="off"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                    class="btn btn-secondary"
                                                                    data-dismiss="modal">
                                                                <i class="fa fa-times"></i>
                                                                Cancel
                                                            </button>

                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fa fa-trash"></i>
                                                                Delete Backup
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <i class="fa fa-archive"></i>
                                        <strong>No database backups found.</strong>
                                        <div class="mt-1">Create your first backup to get started.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')