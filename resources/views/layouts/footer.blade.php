@php
    $systemSettings = $systemSettings ?? \App\Models\SystemSetting::current();
@endphp

<style>
    /*
     * Remove the large empty space created
     * by the theme's content minimum height.
     */
    .content-body {
        min-height: auto !important;
        height: auto !important;
        padding-bottom: 0 !important;
    }

    .content-body .container-fluid {
        padding-bottom: 10px !important;
    }

    /*
     * Keep footer after the content.
     * It will not remain stuck while scrolling.
     */
    .footer {
        position: static !important;
        right: auto !important;
        bottom: auto !important;
        left: auto !important;
        width: auto !important;
        min-height: auto !important;
        height: auto !important;
        margin-top: 0 !important;
    }

    body {
        padding-bottom: 0 !important;
    }
</style>

<div class="footer">
    <div class="copyright">
        <p>
            Copyright &copy; Developed by

            <a href="#" data-system-title>
                {{ $systemSettings->system_title ?? 'RFID-BARM System' }}
            </a>

            {{ date('Y') }}
        </p>
    </div>
</div>

</div>

@include('layouts.js')
@include('layouts.system_settings_live')

</body>
</html>
