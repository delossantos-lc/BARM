@php
    $systemSettings = $systemSettings ?? \App\Models\SystemSetting::current();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title data-system-title-mode="system">{{ $systemSettings->system_title ?? 'RFID-BARM System' }}</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ !empty($systemSettings->favicon_path) ? asset('storage/' . $systemSettings->favicon_path) : asset('assets/images/favicon.png') }}">
