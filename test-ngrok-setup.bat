@echo off
chcp 65001 >nul
echo ========================================
echo 🧪 TEST: Verificación de Configuración
echo ========================================
echo.

echo [1/5] Verificando que ngrok está instalado...
where ngrok >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ ngrok encontrado
) else (
    if exist ngrok.exe (
        echo ✅ ngrok.exe encontrado en carpeta del proyecto
    ) else (
        echo ❌ ngrok NO encontrado
        echo    Por favor instala ngrok desde: https://ngrok.com/download
        pause
        exit /b 1
    )
)
echo.

echo [2/5] Verificando versión de ngrok...
if exist ngrok.exe (
    for /f "tokens=3" %%v in ('ngrok.exe version 2^>nul') do (
        echo ✅ ngrok versión: %%v
        set NGROK_VERSION=%%v
    )
) else (
    for /f "tokens=3" %%v in ('ngrok version 2^>nul') do (
        echo ✅ ngrok versión: %%v
        set NGROK_VERSION=%%v
    )
)
echo.

echo [3/5] Verificando configuración de Laravel...
php artisan --version >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ Laravel funcionando correctamente
) else (
    echo ❌ Error en Laravel
    pause
    exit /b 1
)
echo.

echo [4/5] Verificando rutas necesarias...
php artisan route:list | findstr "mobile-config" >nul
if %errorlevel% equ 0 (
    echo ✅ Ruta /mobile-config registrada
) else (
    echo ❌ Ruta /mobile-config NO encontrada
    pause
    exit /b 1
)

php artisan route:list | findstr "server-info" >nul
if %errorlevel% equ 0 (
    echo ✅ Ruta /api/server-info registrada
) else (
    echo ❌ Ruta /api/server-info NO encontrada
    pause
    exit /b 1
)
echo.

echo [5/5] Verificando vista mobile-config...
if exist "resources\views\mobile-config.blade.php" (
    echo ✅ Vista mobile-config.blade.php existe
) else (
    echo ❌ Vista mobile-config.blade.php NO encontrada
    pause
    exit /b 1
)
echo.

echo ========================================
echo ✅ TODAS LAS VERIFICACIONES PASARON
echo ========================================
echo.
echo 📋 Resumen:
echo    • ngrok: ✅ Instalado
echo    • Laravel: ✅ Funcionando
echo    • Rutas: ✅ Registradas
echo    • Vista: ✅ Existe
echo.
echo 🚀 TODO LISTO para usar start-with-ngrok.bat
echo.
echo ========================================
echo 📱 Scripts disponibles:
echo ========================================
echo.
echo    start-with-ngrok.bat  → Iniciar con ngrok (presentaciones)
echo    start-server.bat      → Iniciar servidor local (desarrollo)
echo    show-qr.bat          → Mostrar QR de configuración
echo.
echo ========================================
echo.

pause
