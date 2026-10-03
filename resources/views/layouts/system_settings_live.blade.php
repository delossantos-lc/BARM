<script>
(function () {
    'use strict';

    const storageKey = 'barm_system_settings_updated';
    const channel = 'BroadcastChannel' in window
        ? new BroadcastChannel('barm-system-settings')
        : null;

    function setText(selector, value) {
        if (value === undefined || value === null) return;
        document.querySelectorAll(selector).forEach(function (element) {
            element.textContent = value;
        });
    }

    function apply(settings) {
        if (!settings || typeof settings !== 'object') return;

        setText('[data-system-title]', settings.system_title);
        setText('[data-system-short-name]', settings.system_short_name);
        setText('[data-institution-name]', settings.institution_name);
        setText('[data-department-name]', settings.department_name);
        setText('[data-attendance-kiosk-title]', settings.attendance_kiosk_title);
        setText('[data-attendance-kiosk-subtitle]', settings.attendance_kiosk_subtitle);
        setText('[data-reservation-instructions]', settings.reservation_pickup_instructions);

        if (settings.logo_url) {
            document.querySelectorAll('[data-system-logo]').forEach(function (image) {
                image.src = settings.logo_url;
            });
        }

        const title = document.querySelector('title');
        if (title && title.dataset.systemTitleMode === 'system') {
            title.textContent = settings.system_title || title.textContent;
        } else if (title && title.dataset.systemTitleMode === 'attendance') {
            title.textContent = settings.attendance_kiosk_title || title.textContent;
        } else if (title && title.dataset.systemTitlePrefix) {
            title.textContent = title.dataset.systemTitlePrefix + ' | ' +
                (settings.system_short_name || 'BARM');
        }
    }

    window.applyBarmSystemSettings = apply;
    window.publishBarmSystemSettings = function (settings) {
        apply(settings);

        if (channel) channel.postMessage(settings);

        try {
            localStorage.setItem(storageKey, JSON.stringify({
                settings: settings,
                updatedAt: Date.now()
            }));
        } catch (error) {
            // The current tab was already updated; storage may be disabled.
        }
    };

    if (channel) {
        channel.addEventListener('message', function (event) {
            apply(event.data);
        });
    }

    window.addEventListener('storage', function (event) {
        if (event.key !== storageKey || !event.newValue) return;

        try {
            apply(JSON.parse(event.newValue).settings);
        } catch (error) {
            // Ignore malformed values written by another script.
        }
    });
})();
</script>
