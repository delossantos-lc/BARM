<?php

return [
    'directory' => storage_path('app/private/database-backups'),

    // For XAMPP on Windows, use for example:
    // C:\\xampp\\mysql\\bin\\mysqldump.exe
    'mysqldump_binary' => env('MYSQLDUMP_BINARY', 'mysqldump'),

    // For XAMPP on Windows, use for example:
    // C:\\xampp\\mysql\\bin\\mysql.exe
    'mysql_binary' => env('MYSQL_BINARY', 'mysql'),

    'timeout_seconds' => (int) env('DATABASE_BACKUP_TIMEOUT', 300),
    'maximum_upload_kilobytes' => (int) env('DATABASE_RESTORE_MAX_KB', 102400),
];
