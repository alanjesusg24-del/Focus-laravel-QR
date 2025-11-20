@echo off
echo ========================================
echo Order QR System - Auto Network Setup
echo ========================================
echo.

REM Get the active network IP address
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    set IP=%%a
    goto :found
)

:found
REM Trim leading spaces
set IP=%IP: =%

echo Detected IP Address: %IP%
echo.

REM Update .env file with current IP
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=http://%IP%:8000' | Set-Content .env"

echo Updated .env with APP_URL=http://%IP%:8000
echo.

REM Generate QR code URL for mobile app
echo ========================================
echo MOBILE APP CONFIGURATION
echo ========================================
echo.
echo Scan this QR to configure your mobile app:
echo URL: http://%IP%:8000/api
echo.
echo Open this URL in your browser to get the QR code:
echo http://%IP%:8000/mobile-config
echo.

REM Clear config cache
echo Clearing Laravel config cache...
php artisan config:clear
php artisan cache:clear
echo.

echo ========================================
echo Starting Laravel Server...
echo ========================================
echo Server will be available at: http://%IP%:8000
echo Press Ctrl+C to stop the server
echo.

REM Start the server
php artisan serve --host=0.0.0.0 --port=8000
