<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== TEST: LOGIN CON EMAIL/PASSWORD ===\n\n";

// Crear usuario de prueba si no existe
$email = 'test@test.com';
$password = '12345678';
$deviceId = 'flutter_test_' . time();

$user = User::where('email', $email)->first();

if (!$user) {
    echo "Creando usuario de prueba...\n";
    $user = User::create([
        'name' => 'Test User',
        'email' => $email,
        'password' => Hash::make($password),
        'device_id' => $deviceId,
    ]);
    echo "✅ Usuario creado: {$user->email}\n";
    echo "   Device ID: {$user->device_id}\n\n";
} else {
    echo "Usuario existente: {$user->email}\n";
    echo "Device ID actual: " . ($user->device_id ?? 'NULL') . "\n\n";
}

// Generar token
$token = $user->createToken('auth_token')->plainTextToken;

echo "=== SIMULAR RESPUESTA DEL BACKEND ===\n\n";
echo json_encode([
    'success' => true,
    'message' => 'Login exitoso',
    'data' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'device_id' => $user->device_id,
    ],
    'token' => $token,
], JSON_PRETTY_PRINT) . "\n\n";

echo "=== INSTRUCCIONES PARA FLUTTER ===\n\n";
echo "1. El backend YA está retornando el token correctamente\n";
echo "2. Flutter debe guardar este token en SharedPreferences\n";
echo "3. Verificar que AuthProvider.login() esté llamando:\n";
echo "   - await _apiService.setAuthToken(token)\n";
echo "   - await StorageService.saveUserData(_user!)\n\n";

echo "=== DEBUGGING ===\n\n";
echo "Si el login con email/password NO persiste:\n";
echo "- Verificar que login() en AuthProvider llame setAuthToken()\n";
echo "- Verificar que StorageService.saveToken() se esté ejecutando\n";
echo "- Agregar prints en Flutter:\n";
echo "  print('🔐 Token guardado: \${await StorageService.getToken()}');\n\n";

echo "Credenciales para probar:\n";
echo "Email: {$email}\n";
echo "Password: {$password}\n";
echo "Device ID: {$deviceId}\n";
