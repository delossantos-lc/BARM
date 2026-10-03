<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $systemSettings->system_short_name ?? 'BARM' }} — Learning Commons</title>

    {{-- Shared theme variables + layout offsets --}}
    @include('partials.theme-vars')

    {{-- Page-specific styles --}}
    @stack('styles')
</head>
<body>

    {{-- ============================================================
         SIDEBAR (fixed, off-canvas on mobile)
         ============================================================ --}}
    @include('partials.sidebar')

    {{-- ============================================================
         FIXED HEADER (shifts right of sidebar on desktop)
         ============================================================ --}}
    @include('partials.header')

    {{-- ============================================================
         MAIN CONTENT
         Offset by margin-left (sidebar) + padding-top (header).
         Both offsets collapse to 0 / reset on mobile.
         ============================================================ --}}
    <div class="lc-main-content">
        @yield('content')
    </div>

    {{-- Page-specific scripts --}}
    @stack('scripts')
</body>
</html>