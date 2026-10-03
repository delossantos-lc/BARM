<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookReservationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailNotificationController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\LibraryPolicyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\OverdueController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentPersonnelController;
use App\Http\Controllers\StudentPersonnelManagementController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseBackupController;

// Guest
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.authenticate');
});

// Admin and staff pages
Route::middleware('auth')->group(function () {
    Route::get('/index', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/profile', 'profile')->name('profile');

    // Student and personnel management
    Route::get('/student-personnel-management', [StudentPersonnelManagementController::class, 'index'])
        ->name('studentpersonnel.management');
    Route::post('/student-personnel-management/students', [StudentPersonnelManagementController::class, 'storeStudent'])
        ->name('management.students.store');
    Route::put('/student-personnel-management/students/{student}', [StudentPersonnelManagementController::class, 'updateStudent'])
        ->name('management.students.update');
    Route::delete('/student-personnel-management/students/{student}', [StudentPersonnelManagementController::class, 'destroyStudent'])
        ->name('management.students.destroy');
    Route::post('/student-personnel-management/personnel', [StudentPersonnelManagementController::class, 'storePersonnel'])
        ->name('management.personnel.store');
    Route::put('/student-personnel-management/personnel/{personnel}', [StudentPersonnelManagementController::class, 'updatePersonnel'])
        ->name('management.personnel.update');
    Route::delete('/student-personnel-management/personnel/{personnel}', [StudentPersonnelManagementController::class, 'destroyPersonnel'])
        ->name('management.personnel.destroy');

    // User management
    Route::get('/user-management', [UserManagementController::class, 'index'])->name('user.management');
    Route::post('/user-management/staff', [UserManagementController::class, 'store'])->name('staff.store');
    Route::put('/user-management/staff/{user}', [UserManagementController::class, 'update'])->name('staff.update');
    Route::delete('/user-management/staff/{user}', [UserManagementController::class, 'destroy'])->name('staff.destroy');

    // Attendance monitoring
    Route::get('/students', [StudentPersonnelController::class, 'students'])->name('students.monitoring');
    Route::get('/student-monitoring/pdf', [StudentPersonnelController::class, 'studentsPdf'])
        ->name('students.monitoring.pdf');
    Route::get('/personnel', [StudentPersonnelController::class, 'personnel'])->name('personnel.monitoring');
    Route::get('/personnel-monitoring/pdf', [StudentPersonnelController::class, 'personnelPdf'])
        ->name('personnel.monitoring.pdf');

    // Books
    Route::get('/books', [BookController::class, 'index'])->name('book');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::post('/books/import-csv', [BookController::class, 'importCsv'])->name('books.import.csv');
    Route::get('/books/import-template', [BookController::class, 'downloadCsvTemplate'])
        ->name('books.import.template');

    // Borrow monitoring
    Route::get('/bookborrow-data', [BookController::class, 'borrowMonitoringData'])->name('bookborrow.data');
    Route::get('/bookborrow', [BookController::class, 'borrowMonitoring'])->name('bookborrow');
    Route::put('/bookborrow/{id}/return', [BookController::class, 'returnBook'])->name('bookborrow.return');

    // Reservation monitoring
    Route::get('/reserve-monitoring', [BookReservationController::class, 'index'])->name('reserve.monitoring');
    Route::put('/reserve-monitoring/{id}/ready', [BookReservationController::class, 'ready'])->name('reserve.ready');
    Route::put('/reserve-monitoring/{id}/pickup', [BookReservationController::class, 'pickup'])->name('reserve.pickup');
    Route::put('/reserve-monitoring/{id}/cancel', [BookReservationController::class, 'cancel'])->name('reserve.cancel');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Public attendance kiosk
Route::view('/attendance', 'attendance')->name('attendance.kiosk');
Route::get('/monitor', [MonitorController::class, 'monitor'])->name('monitor');
Route::post('/attendance/scan', [AttendanceController::class, 'scanRfid'])->name('attendance.scan');

// Borrow kiosk
Route::post('/borrow-enter', function () {
    session(['borrow_kiosk_access' => true]);

    return redirect()->route('borrow.kiosk');
})->name('borrow.enter');

Route::get('/borrowers/find-by-rfid/{rfid}', [BookController::class, 'findBorrowerByRfid'])
    ->name('borrowers.findByRfid');

Route::get('/borrowkiosk', function () {
    if (!session('borrow_kiosk_access')) {
        return redirect()
            ->route('monitor')
            ->with('error', 'Please click the BORROW button first.');
    }

    return app(BookController::class)->borrowForm();
})->name('borrow.kiosk');

Route::post('/borrowkiosk/confirm', [BookController::class, 'storeBorrow'])->name('borrow.store');

Route::get('/borrowkiosk/exit', function () {
    session()->forget('borrow_kiosk_access');

    return redirect()->route('monitor');
})->name('borrow.exit');

// Return kiosk
Route::post('/return-enter', function () {
    session(['return_kiosk_access' => true]);

    return redirect()->route('return');
})->name('return.enter');

Route::get('/return', function () {
    if (!session('return_kiosk_access')) {
        return redirect()
            ->route('monitor')
            ->with('error', 'Please click the RETURN button first.');
    }

    return app(BookController::class)->returnKiosk();
})->name('return');

Route::get('/return/borrowers/find-by-rfid/{rfid}', [BookController::class, 'findReturnBorrowerByRfid'])
    ->name('return.borrower.find');
Route::post('/return/confirm', [BookController::class, 'storeReturn'])->name('return.store');

Route::get('/return/exit', function () {
    session()->forget('return_kiosk_access');

    return redirect()->route('monitor');
})->name('return.exit');

// Reservation kiosk
Route::get('/reserve/student/rfid/{rfid}', [BookReservationController::class, 'findStudentByRfid'])
    ->name('reserve.student.rfid');
Route::get('/reserve', [BookReservationController::class, 'create'])->name('reserve');
Route::post('/reserve', [BookReservationController::class, 'store'])->name('reserve.store');

// Overdue records
Route::get('/overdues', [OverdueController::class, 'index'])->name('overdue');
Route::post('/overdues/{fine}/paid', [OverdueController::class, 'markPaid'])->name('overdue.paid');
Route::post('/overdues/{fine}/waive', [OverdueController::class, 'waive'])->name('overdue.waive');
Route::post('/overdues/{borrow}/lost', [OverdueController::class, 'reportLost'])->name('overdue.lost');
Route::post('/overdues/{borrow}/damaged', [OverdueController::class, 'reportDamaged'])->name('overdue.damaged');
Route::post('/overdues/{fine}/replacement-payment', [OverdueController::class, 'recordReplacementPayment'])
    ->name('overdue.replacement-payment');
Route::get('/overdues/{fine}/acknowledgement', [OverdueController::class, 'acknowledgement'])
    ->name('overdue.acknowledgement');

// Policy, audit logs, and reports
Route::get('/admin/policy', [LibraryPolicyController::class, 'index'])->name('policy.index');
Route::put('/admin/policy', [LibraryPolicyController::class, 'update'])->name('policy.update');
Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.log');
Route::get('/audit-logs/pdf', [AuditLogController::class, 'exportPdf'])->name('audit.log.pdf');
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/pdf', [ReportController::class, 'pdf'])
    ->name('reports.pdf');

// System settings
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SystemSettingController::class, 'update'])->name('settings.update');
});

Route::post('/settings/email/test', [EmailNotificationController::class, 'test'])
    ->middleware('auth')
    ->name('email.test');

// Fingerprint
Route::post('/fingerprint/find', [FingerprintController::class, 'find'])->name('fingerprint.find');

Route::middleware('auth')->group(function () {
    Route::post('/fingerprint/assign', [FingerprintController::class, 'assign'])->name('fingerprint.assign');
    Route::delete('/fingerprint/remove', [FingerprintController::class, 'remove'])->name('fingerprint.remove');
    Route::get('/fingerprint/next-available', [FingerprintController::class, 'nextAvailable'])
        ->name('fingerprint.next-available');
});

// Backup
Route::prefix('database')->group(function () {
    Route::get('/backup', [DatabaseBackupController::class, 'backupPage'])
        ->name('database.backup.page');
    Route::post('/backup', [DatabaseBackupController::class, 'createBackup'])
        ->name('database.backup.create');
    Route::get('/backup/{filename}/download', [DatabaseBackupController::class, 'downloadBackup'])
        ->where('filename', '[A-Za-z0-9._-]+')
        ->name('database.backup.download');
    Route::delete('/backup/{filename}', [DatabaseBackupController::class, 'deleteBackup'])
        ->where('filename', '[A-Za-z0-9._-]+')
        ->name('database.backup.delete');

    Route::get('/restore', [DatabaseBackupController::class, 'restorePage'])
        ->name('database.restore.page');
    Route::post('/restore', [DatabaseBackupController::class, 'restoreDatabase'])
        ->name('database.restore.run');
});
