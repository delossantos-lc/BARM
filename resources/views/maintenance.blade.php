<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance | {{ $settings->system_title }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 24px;
            background: linear-gradient(135deg, #fdecec, #f7c8d0);
            color: #292929;
            font-family: Arial, Helvetica, sans-serif;
        }
        .maintenance-card {
            width: 100%;
            max-width: 620px;
            padding: 45px 35px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.92);
            text-align: center;
            box-shadow: 0 24px 60px rgba(128, 62, 84, 0.20);
        }
        .maintenance-logo {
            width: 110px;
            height: 110px;
            margin-bottom: 18px;
            object-fit: contain;
        }
        h1 { margin: 0 0 12px; font-size: 30px; }
        p { margin: 0; color: #666; font-size: 16px; line-height: 1.7; }
        .system-name { margin-top: 24px; color: #d55b91; font-weight: 700; }
    </style>
</head>
<body>
    <main class="maintenance-card">
        <img src="{{ $settings->logoUrl() }}" class="maintenance-logo" alt="System logo">
        <h1>System Maintenance</h1>
        <p>{{ $settings->maintenance_message }}</p>
        <div class="system-name">{{ $settings->system_title }}</div>
    </main>
</body>
</html>
