<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\Process\Process;
use Throwable;

class DatabaseBackupController extends Controller
{
    public function backupPage()
    {
        $this->ensureAdmin();

        return view('backup', [
            'backups' => $this->availableBackups(),
        ]);
    }

    public function restorePage()
    {
        $this->ensureAdmin();

        return view('restore', [
            'backups' => $this->availableBackups(),
        ]);
    }

    public function createBackup(Request $request)
    {
        $this->ensureAdmin();

        try {
            $path = $this->makeBackup('manual');
            $filename = basename($path);

            $this->audit(
                'Database Backup Created',
                $filename,
                'success',
                'A complete database backup was created.',
                $request
            );

            return back()->with(
                'success',
                'Database backup created successfully: ' . $filename
            );
        } catch (Throwable $exception) {
            report($exception);

            $this->audit(
                'Database Backup Failed',
                null,
                'failed',
                $exception->getMessage(),
                $request
            );

            return back()->with(
                'error',
                'Backup failed: ' . $exception->getMessage()
            );
        }
    }

    public function downloadBackup(string $filename)
    {
        $this->ensureAdmin();

        $path = $this->resolveBackupPath($filename);

        abort_unless($path && File::isFile($path), 404, 'Backup file not found.');

        return response()->download($path, basename($path), [
            'Content-Type' => 'application/sql',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function deleteBackup(Request $request, string $filename)
    {
        $this->ensureAdmin();

        $request->validate([
            'delete_confirmation' => ['required', Rule::in(['DELETE'])],
        ]);

        $path = $this->resolveBackupPath($filename);

        if (!$path || !File::isFile($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        File::delete($path);

        $this->audit(
            'Database Backup Deleted',
            basename($path),
            'success',
            'A stored database backup was deleted.',
            $request
        );

        return back()->with('success', 'Backup deleted successfully.');
    }

    public function restoreDatabase(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'restore_source' => ['required', Rule::in(['saved', 'upload'])],
            'backup_file' => [
                Rule::requiredIf(fn () => $request->input('restore_source') === 'saved'),
                'nullable',
                'string',
            ],
            'sql_file' => [
                Rule::requiredIf(fn () => $request->input('restore_source') === 'upload'),
                'nullable',
                'file',
                'mimes:sql,txt',
                'max:' . config('database_backup.maximum_upload_kilobytes', 102400),
            ],
            'current_password' => ['required', 'string'],
            'restore_confirmation' => ['required', Rule::in(['RESTORE'])],
        ]);

        $user = auth()->user();

        if (!$user || !Hash::check($validated['current_password'], $user->password)) {
            $this->audit(
                'Database Restore Rejected',
                null,
                'failed',
                'The administrator password was incorrect.',
                $request
            );

            return back()->withInput()->with(
                'error',
                'The administrator password is incorrect.'
            );
        }

        $temporaryUpload = null;

        try {
            if ($validated['restore_source'] === 'saved') {
                $restorePath = $this->resolveBackupPath($validated['backup_file']);

                if (!$restorePath || !File::isFile($restorePath)) {
                    throw new \RuntimeException('The selected backup file does not exist.');
                }
            } else {
                $temporaryDirectory = storage_path('app/private/database-restore-uploads');
                File::ensureDirectoryExists($temporaryDirectory);

                $temporaryUpload = $request->file('sql_file')->move(
                    $temporaryDirectory,
                    'restore-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.sql'
                );

                $restorePath = $temporaryUpload->getPathname();
            }

            $this->validateSqlFile($restorePath);

            // Always create a recovery point before changing the database.
            $safetyBackup = $this->makeBackup('before-restore');

            $this->runRestore($restorePath);

            $this->audit(
                'Database Restored',
                basename($restorePath),
                'success',
                'Database restored successfully. Safety backup: '
                    . basename($safetyBackup),
                $request
            );

            return redirect()
                ->route('database.restore.page')
                ->with(
                    'success',
                    'Database restored successfully. A safety backup was created first.'
                );
        } catch (Throwable $exception) {
            report($exception);

            $this->audit(
                'Database Restore Failed',
                isset($restorePath) ? basename($restorePath) : null,
                'failed',
                $exception->getMessage(),
                $request
            );

            return back()->withInput()->with(
                'error',
                'Restore failed: ' . $exception->getMessage()
            );
        } finally {
            if ($temporaryUpload && File::exists($temporaryUpload->getPathname())) {
                File::delete($temporaryUpload->getPathname());
            }
        }
    }

    private function makeBackup(string $label): string
    {
        $database = $this->databaseConfiguration();
        $directory = config('database_backup.directory');
        File::ensureDirectoryExists($directory);

        $filename = sprintf(
            '%s-%s-%s.sql',
            preg_replace('/[^A-Za-z0-9_-]/', '_', $database['database']),
            $label,
            now()->format('Ymd-His')
        );

        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        $command = [
            config('database_backup.mysqldump_binary', 'mysqldump'),
            '--protocol=TCP',
            '--host=' . $database['host'],
            '--port=' . $database['port'],
            '--user=' . $database['username'],
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--events',
            '--hex-blob',
            '--default-character-set=utf8mb4',
            '--result-file=' . $path,
            $database['database'],
        ];

        $processEnvironment = !empty($database['password'])
            ? ['MYSQL_PWD' => (string) $database['password']]
            : null;

        $process = new Process(
            $command,
            null,
            $processEnvironment
        );

        $process->setTimeout(config('database_backup.timeout_seconds', 300));
        $process->run();

        if (!$process->isSuccessful() || !File::isFile($path) || File::size($path) === 0) {
            File::delete($path);

            throw new \RuntimeException(
                trim($process->getErrorOutput()) ?: 'mysqldump did not create a valid backup.'
            );
        }

        return $path;
    }

    private function runRestore(string $sqlPath): void
    {
        $database = $this->databaseConfiguration();

        $processEnvironment = !empty($database['password'])
            ? ['MYSQL_PWD' => (string) $database['password']]
            : null;

        $process = new Process(
            [
                config('database_backup.mysql_binary', 'mysql'),
                '--protocol=TCP',
                '--host=' . $database['host'],
                '--port=' . $database['port'],
                '--user=' . $database['username'],
                '--default-character-set=utf8mb4',
                $database['database'],
            ],
            null,
            $processEnvironment
        );

        $stream = fopen($sqlPath, 'rb');

        if ($stream === false) {
            throw new \RuntimeException('The SQL backup could not be opened.');
        }

        try {
            $process->setInput($stream);
            $process->setTimeout(config('database_backup.timeout_seconds', 300));
            $process->run();
        } finally {
            fclose($stream);
        }

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                trim($process->getErrorOutput()) ?: 'The MySQL restore command failed.'
            );
        }
    }

    private function databaseConfiguration(): array
    {
        $connectionName = config('database.default');
        $connection = config('database.connections.' . $connectionName, []);

        if (($connection['driver'] ?? null) !== 'mysql') {
            throw new \RuntimeException('Backup and restore currently require a MySQL connection.');
        }

        if (empty($connection['database']) || empty($connection['username'])) {
            throw new \RuntimeException('The MySQL database configuration is incomplete.');
        }

        return [
            'host' => $connection['host'] ?? '127.0.0.1',
            'port' => $connection['port'] ?? 3306,
            'database' => $connection['database'],
            'username' => $connection['username'],
            'password' => $connection['password'] ?? '',
        ];
    }

    private function availableBackups(): array
    {
        $directory = config('database_backup.directory');
        File::ensureDirectoryExists($directory);

        return collect(File::files($directory))
            ->filter(fn ($file) => strtolower($file->getExtension()) === 'sql')
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'created_at' => date('M d, Y h:i A', $file->getMTime()),
            ])
            ->values()
            ->all();
    }

    private function resolveBackupPath(string $filename): ?string
    {
        $safeFilename = basename($filename);

        if ($safeFilename !== $filename || !str_ends_with(strtolower($safeFilename), '.sql')) {
            return null;
        }

        return config('database_backup.directory')
            . DIRECTORY_SEPARATOR
            . $safeFilename;
    }

    private function validateSqlFile(string $path): void
    {
        if (!File::isFile($path) || File::size($path) === 0) {
            throw new \RuntimeException('The selected SQL file is empty or invalid.');
        }

        $handle = fopen($path, 'rb');
        $sample = $handle ? fread($handle, 8192) : false;

        if ($handle) {
            fclose($handle);
        }

        if ($sample === false || str_contains($sample, "\0")) {
            throw new \RuntimeException('The selected file is not a valid text SQL backup.');
        }

        if (!preg_match('/\b(CREATE|INSERT|ALTER|DROP|SET|LOCK)\b/i', $sample)) {
            throw new \RuntimeException('The selected file does not appear to contain SQL backup commands.');
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        return number_format(max(1, $bytes / 1024), 2) . ' KB';
    }

    private function ensureAdmin(): void
    {
        abort_unless(
            strtolower((string) session('session_access_level', '')) === 'admin',
            403,
            'Unauthorized access.'
        );
    }

    private function audit(
        string $action,
        ?string $record,
        string $result,
        string $description,
        Request $request
    ): void {
        try {
            AuditLogger::record(
                $action,
                'Backup and Restore',
                $record,
                null,
                null,
                $result,
                mb_substr($description, 0, 1000),
                $request
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
