@echo off
cd /d "%~dp0"

if not exist node_modules (
    echo Installing fingerprint bridge packages...
    call npm install
)

set FINGERPRINT_PORT=COM3
set FINGERPRINT_BAUD=9600
node server.js

pause
