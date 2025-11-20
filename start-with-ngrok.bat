@echo off
chcp 65001 >nul
echo ========================================
echo 🚀 Order QR System - ngrok Setup
echo ========================================
echo.

REM Check if ngrok is installed
where ngrok >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ ngrok no está instalado.
    echo.
    echo 📥 Por favor, descarga e instala ngrok:
    echo    1. Ve a: https://ngrok.com/download
    echo    2. Descarga ngrok para Windows
    echo    3. Extrae ngrok.exe a una carpeta
    echo    4. Agrega esa carpeta al PATH de Windows
    echo.
    echo 💡 O coloca ngrok.exe en esta carpeta del proyecto
    echo.
    pause
    exit /b 1
)

echo ✅ ngrok encontrado
echo.

REM Clear Laravel cache
echo 🧹 Limpiando caché de Laravel...
php artisan config:clear >nul 2>nul
php artisan cache:clear >nul 2>nul
php artisan route:clear >nul 2>nul
echo ✅ Caché limpiada
echo.

REM Start Laravel server in background
echo 🔧 Iniciando servidor Laravel en segundo plano...
start /B php artisan serve --host=127.0.0.1 --port=8000 >nul 2>nul
timeout /t 3 /nobreak >nul
echo ✅ Servidor Laravel iniciado en puerto 8000
echo.

REM Start ngrok and capture the URL
echo 🌐 Iniciando ngrok tunnel...
echo.
echo ========================================
echo 📱 CONFIGURACIÓN DE LA APP MÓVIL
echo ========================================
echo.

REM Create a temp file to store ngrok process
echo @echo off > kill-ngrok.bat
echo taskkill /F /IM ngrok.exe 2^>nul >> kill-ngrok.bat
echo taskkill /F /FI "WINDOWTITLE eq Order QR System*" 2^>nul >> kill-ngrok.bat
echo del kill-ngrok.bat 2^>nul >> kill-ngrok.bat

echo 🔗 ngrok se está iniciando...
echo.
echo ⏳ Espera 5 segundos para obtener la URL pública...
echo.

REM Start ngrok in a new window
start "ngrok - Order QR System" ngrok http 8000 --log=stdout

REM Wait for ngrok to start
timeout /t 5 /nobreak >nul

REM Get ngrok URL from API
echo 📡 Obteniendo URL pública de ngrok...
echo.

REM Use PowerShell to get ngrok URL
for /f "delims=" %%i in ('powershell -Command "try { $response = Invoke-RestMethod -Uri 'http://localhost:4040/api/tunnels' -ErrorAction Stop; $url = $response.tunnels[0].public_url; if ($url) { $url } else { 'ERROR' } } catch { 'ERROR' }"') do set NGROK_URL=%%i

if "%NGROK_URL%"=="ERROR" (
    echo ⚠️ No se pudo obtener la URL automáticamente.
    echo.
    echo 🔍 Por favor, abre tu navegador en: http://localhost:4040
    echo    y copia la URL pública que aparece allí.
    echo.
    echo Presiona cualquier tecla después de copiar la URL...
    pause >nul
    set /p NGROK_URL="📝 Pega la URL de ngrok aquí (ej: https://abc123.ngrok.io): "
)

echo.
echo ========================================
echo ✅ SERVIDOR PÚBLICO ACTIVO
echo ========================================
echo.
echo 🌐 URL Pública: %NGROK_URL%
echo 🔗 API URL: %NGROK_URL%/api
echo.

REM Update .env with ngrok URL
echo 💾 Actualizando configuración de Laravel...
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=%NGROK_URL%' | Set-Content .env"
php artisan config:clear >nul 2>nul
echo ✅ Configuración actualizada
echo.

echo ========================================
echo 📱 CONFIGURAR APP MÓVIL
echo ========================================
echo.
echo 🎯 Opción 1: Escanear QR
echo    Abre esta URL en tu navegador:
echo    %NGROK_URL%/mobile-config
echo.
echo 📋 Opción 2: Copiar manualmente
echo    Configura esta URL en la app móvil:
echo    %NGROK_URL%/api
echo.
echo ========================================
echo 🖥️  PANEL DE CONTROL
echo ========================================
echo.
echo 🌐 ngrok Dashboard: http://localhost:4040
echo    (Ver estadísticas, requests, etc.)
echo.
echo 🏠 Admin Laravel: %NGROK_URL%/business/login
echo    Usuario: admin@example.com
echo    Contraseña: password
echo.
echo ========================================
echo ⚠️  IMPORTANTE
echo ========================================
echo.
echo ⏰ La URL de ngrok es temporal y cambia cada vez
echo    que reinicias el script.
echo.
echo 💡 Para una URL permanente, crea una cuenta en:
echo    https://dashboard.ngrok.com/get-started/setup
echo    y usa: ngrok http 8000 --domain=TU-DOMINIO.ngrok.io
echo.
echo 🛑 Para DETENER el servidor:
echo    - Cierra esta ventana Y la ventana de ngrok
echo    - O ejecuta: kill-ngrok.bat
echo.
echo ========================================
echo.
echo ✅ Todo listo! Presiona Ctrl+C para detener
echo.

REM Keep the window open
pause >nul

REM Cleanup on exit
echo.
echo 🛑 Deteniendo servicios...
taskkill /F /IM ngrok.exe >nul 2>nul
taskkill /F /FI "WINDOWTITLE eq Administrator*php*" >nul 2>nul
echo ✅ Servicios detenidos
