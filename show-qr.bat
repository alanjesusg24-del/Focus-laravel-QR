@echo off
chcp 65001 >nul
echo ========================================
echo 📱 Mostrar QR de Configuración
echo ========================================
echo.

REM Get current APP_URL from .env
for /f "tokens=2 delims==" %%a in ('findstr "APP_URL" .env') do set APP_URL=%%a

REM Remove quotes if present
set APP_URL=%APP_URL:"=%

echo 🌐 URL del Servidor: %APP_URL%
echo.
echo 📱 Abre esta URL en tu navegador para ver el QR:
echo.
echo    %APP_URL%/mobile-config
echo.
echo ========================================
echo.

REM Try to open in default browser
start "" "%APP_URL%/mobile-config"

echo ✅ Se abrió el navegador automáticamente
echo.
echo Si no se abrió, copia y pega la URL de arriba
echo.
pause
