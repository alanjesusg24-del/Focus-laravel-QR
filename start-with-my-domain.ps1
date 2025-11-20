# Order QR System - Iniciar con ngrok (PowerShell)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "🚀 Order QR System - Dominio Fijo" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Update .env with ngrok URL
Write-Host "💾 Actualizando configuración de Laravel..." -ForegroundColor Yellow
$envContent = Get-Content .env -Raw
$envContent = $envContent -replace 'APP_URL=.*', 'APP_URL=https://gerald-ironical-contradictorily.ngrok-free.dev'
Set-Content .env $envContent
php artisan config:clear | Out-Null
php artisan cache:clear | Out-Null
Write-Host "✅ Configuración actualizada" -ForegroundColor Green
Write-Host ""

# Start Laravel server in background
Write-Host "🔧 Iniciando servidor Laravel..." -ForegroundColor Yellow
Start-Process php -ArgumentList "artisan", "serve", "--host=127.0.0.1", "--port=8000" -WindowStyle Hidden
Start-Sleep -Seconds 3
Write-Host "✅ Laravel corriendo en http://localhost:8000" -ForegroundColor Green
Write-Host ""

Write-Host "========================================" -ForegroundColor Green
Write-Host "✅ SERVIDOR PÚBLICO ACTIVO" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 URL Pública: https://gerald-ironical-contradictorily.ngrok-free.dev" -ForegroundColor Cyan
Write-Host "🔗 API URL: https://gerald-ironical-contradictorily.ngrok-free.dev/api/v1" -ForegroundColor Cyan
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "📱 CONFIGURAR APP MÓVIL" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "La app móvil debe usar esta URL:"
Write-Host "https://gerald-ironical-contradictorily.ngrok-free.dev/api/v1" -ForegroundColor Yellow
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "🌐 INICIAR NGROK" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Start ngrok with fixed domain
ngrok http 8000 --domain=gerald-ironical-contradictorily.ngrok-free.dev
