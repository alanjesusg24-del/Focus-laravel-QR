@echo off
chcp 65001 >nul
echo ========================================
echo 🚀 Order QR System - Dominio Fijo
echo ========================================
echo.

REM Update .env with ngrok URL
echo 💾 Actualizando configuración de Laravel...
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=https://gerald-ironical-contradictorily.ngrok-free.dev' | Set-Content .env"
php artisan config:clear >nul 2>nul
php artisan cache:clear >nul 2>nul
echo ✅ Configuración actualizada
echo.

REM Start Laravel server in background
echo 🔧 Iniciando servidor Laravel...
start /B php artisan serve --host=127.0.0.1 --port=8000
timeout /t 3 /nobreak >nul
echo ✅ Laravel corriendo en http://localhost:8000
echo.

echo ========================================
echo ✅ SERVIDOR PÚBLICO ACTIVO
echo ========================================
echo.
echo 🌐 URL Pública: https://gerald-ironical-contradictorily.ngrok-free.dev
echo 🔗 API URL: https://gerald-ironical-contradictorily.ngrok-free.dev/api/v1
echo.
echo ========================================
echo 📱 CONFIGURAR APP MÓVIL
echo ========================================
echo.
echo La app móvil debe usar esta URL:
echo https://gerald-ironical-contradictorily.ngrok-free.dev/api/v1
echo.
echo ========================================
echo 🌐 INICIAR NGROK
echo ========================================
echo.

REM Start ngrok with fixed domain
ngrok http 8000 --domain=gerald-ironical-contradictorily.ngrok-free.dev
