<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST: TIMEOUT DE SESIÓN Y REDIRECCIÓN ===\n\n";

echo "✅ Cambios aplicados:\n\n";

echo "1. Middleware Authenticate actualizado (app/Http/Middleware/Authenticate.php)\n";
echo "   - Detecta URL actual para determinar el login correcto\n";
echo "   - /business/* → Redirige a business.login\n";
echo "   - /superadmin/* → Redirige a superadmin.login\n";
echo "   - Otras rutas → Redirige a proj.auth.login\n\n";

echo "2. Rutas de business actualizadas (routes/web.php línea 108)\n";
echo "   - Cambio: middleware(['auth']) → middleware(['auth:business'])\n";
echo "   - Esto especifica explícitamente el guard 'business'\n\n";

echo "=== CONFIGURACIÓN ACTUAL ===\n\n";

// Verificar configuración de sesión
$sessionLifetime = config('session.lifetime');
$sessionDriver = config('session.driver');

echo "Session Driver: {$sessionDriver}\n";
echo "Session Lifetime: {$sessionLifetime} minutos\n";
echo "Auth Default Guard: " . config('auth.defaults.guard') . "\n\n";

echo "=== COMPORTAMIENTO ESPERADO ===\n\n";

echo "Escenario 1: Sesión expira en /business/dashboard\n";
echo "  1. Usuario inicia sesión en http://127.0.0.1:8000/business/login\n";
echo "  2. Navega a http://127.0.0.1:8000/business/dashboard\n";
echo "  3. Espera {$sessionLifetime} minutos sin actividad\n";
echo "  4. ✅ Sesión expira, middleware detecta /business/*\n";
echo "  5. ✅ Redirige a http://127.0.0.1:8000/business/login\n";
echo "  6. ❌ NO redirige a /p/order-qr/login\n\n";

echo "Escenario 2: Sesión expira en /superadmin/dashboard\n";
echo "  1. Superadmin inicia sesión\n";
echo "  2. Espera sin actividad\n";
echo "  3. ✅ Redirige a http://127.0.0.1:8000/superadmin/login\n\n";

echo "=== PROBAR ===\n\n";

echo "1. Inicia sesión en http://127.0.0.1:8000/business/login\n";
echo "2. Navega a http://127.0.0.1:8000/business/dashboard\n";
echo "3. Espera {$sessionLifetime} minutos (o modifica config/session.php para testing)\n";
echo "4. Recarga la página o haz clic en cualquier link\n";
echo "5. Verifica que te redirija a /business/login (NO a /p/order-qr/login)\n\n";

echo "=== AJUSTE OPCIONAL PARA TESTING ===\n\n";
echo "Para probar más rápido, edita config/session.php:\n";
echo "  'lifetime' => 1, // 1 minuto en lugar de 120\n\n";

echo "Recuerda revertir el cambio después de probar:\n";
echo "  'lifetime' => 120, // Valor por defecto (2 horas)\n";
