# Guía Completa: Crear Rutas de Autenticación en Laravel Backend

Esta guía te ayudará a crear paso a paso todas las rutas y controladores de autenticación necesarios para tu app Flutter.

## Paso 1: Ubicar tu Proyecto Backend

1. Abre tu proyecto Laravel backend en VSCode o tu editor preferido
2. Verifica que el proyecto esté corriendo correctamente:
```bash
php artisan serve
```

## Paso 2: Instalar Laravel Sanctum (si no está instalado)

Laravel Sanctum se usa para autenticación con tokens.

```bash
# En el directorio de tu backend Laravel
composer require laravel/sanctum

# Publicar configuración de Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Ejecutar migraciones
php artisan migrate
```

## Paso 3: Configurar Sanctum

### 3.1 Agregar HasApiTokens al modelo User

Abre `app/Models/User.php` y agrégale el trait:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Agregar esta línea

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Agregar HasApiTokens aquí

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'profile_photo_url',
        'device_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

### 3.2 Actualizar la migración de users

Si necesitas agregar columnas para Google Sign-In, crea una nueva migración:

```bash
php artisan make:migration add_google_fields_to_users_table --table=users
```

En el archivo de migración creado (en `database/migrations/`):

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('profile_photo_url')->nullable()->after('google_id');
            $table->string('device_id')->nullable()->after('profile_photo_url');
            $table->string('password')->nullable()->change(); // Hacer password opcional para Google
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'profile_photo_url', 'device_id']);
        });
    }
};
```

Ejecuta la migración:
```bash
php artisan migrate
```

## Paso 4: Crear el AuthController

```bash
php artisan make:controller Auth/AuthController
```

Abre `app/Http/Controllers/Auth/AuthController.php` y reemplaza todo el contenido con:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de usuario con email y contraseña
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'device_id' => $request->device_id,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => $user,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login con email y contraseña
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        // Verificar device_id
        if ($user->device_id && $user->device_id !== $request->device_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuario está registrado en otro dispositivo',
                'requires_device_change' => true,
                'user_id' => $user->id,
            ], 403);
        }

        // Actualizar device_id si no existe
        if (!$user->device_id) {
            $user->update(['device_id' => $request->device_id]);
        }

        // Revocar tokens anteriores (opcional)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Login con Google
     */
    public function loginWithGoogle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_id' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'device_id' => 'required|string',
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Buscar usuario por email
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // Crear nuevo usuario
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'google_id' => $request->google_id,
                    'profile_photo_url' => $request->profile_photo_url,
                    'device_id' => $request->device_id,
                    'email_verified_at' => now(), // Google ya verificó el email
                ]);
            } else {
                // Usuario existe, actualizar google_id si es necesario
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $request->google_id,
                        'profile_photo_url' => $request->profile_photo_url,
                    ]);
                }

                // Verificar device_id
                if ($user->device_id && $user->device_id !== $request->device_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este usuario está registrado en otro dispositivo',
                        'requires_device_change' => true,
                        'user_id' => $user->id,
                    ], 403);
                }

                // Actualizar device_id si no existe
                if (!$user->device_id) {
                    $user->update(['device_id' => $request->device_id]);
                }
            }

            // Revocar tokens anteriores
            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso con Google',
                'data' => $user,
                'token' => $token,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en login con Google: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener usuario autenticado
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente',
        ]);
    }

    /**
     * Verificar email (placeholder)
     */
    public function verifyEmail(Request $request)
    {
        // Implementar lógica de verificación de email
        return response()->json([
            'success' => true,
            'message' => 'Email verificado',
        ]);
    }

    /**
     * Reenviar código de verificación (placeholder)
     */
    public function resendVerification(Request $request)
    {
        // Implementar lógica de reenvío
        return response()->json([
            'success' => true,
            'message' => 'Código reenviado',
        ]);
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña actual incorrecta',
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente',
        ]);
    }

    /**
     * Olvidé mi contraseña (placeholder)
     */
    public function forgotPassword(Request $request)
    {
        // Implementar lógica de recuperación
        return response()->json([
            'success' => true,
            'message' => 'Código de recuperación enviado',
        ]);
    }

    /**
     * Resetear contraseña (placeholder)
     */
    public function resetPassword(Request $request)
    {
        // Implementar lógica de reset
        return response()->json([
            'success' => true,
            'message' => 'Contraseña reseteada',
        ]);
    }
}
```

## Paso 5: Crear las Rutas en routes/api.php

Abre `routes/api.php` y agrega estas rutas:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Rutas existentes de mobile...
Route::prefix('v1')->group(function () {
    // Rutas de autenticación
    Route::prefix('auth')->group(function () {
        // Rutas públicas (sin autenticación)
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/login/google', [AuthController::class, 'loginWithGoogle']);
        Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
        Route::post('/password/reset', [AuthController::class, 'resetPassword']);

        // Rutas protegidas (requieren autenticación)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
            Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
            Route::post('/password/change', [AuthController::class, 'changePassword']);
        });
    });

    // Rutas existentes de mobile...
});
```

## Paso 6: Configurar CORS

Abre `config/cors.php` y asegúrate de que esté configurado así:

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // En producción, especifica los dominios permitidos

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

## Paso 7: Probar las Rutas

### Con cURL en PowerShell:

```powershell
# Registro
curl -X POST http://localhost:8000/api/v1/auth/register `
  -H "Content-Type: application/json" `
  -H "Accept: application/json" `
  -d '{\"name\":\"Test User\",\"email\":\"test@test.com\",\"password\":\"12345678\",\"password_confirmation\":\"12345678\",\"device_id\":\"test-device-123\"}'

# Login
curl -X POST http://localhost:8000/api/v1/auth/login `
  -H "Content-Type: application/json" `
  -H "Accept: application/json" `
  -d '{\"email\":\"test@test.com\",\"password\":\"12345678\",\"device_id\":\"test-device-123\"}'
```

## Paso 8: Configurar Ngrok (para probar desde el móvil)

```bash
# Inicia ngrok apuntando al puerto de Laravel (usualmente 8000)
ngrok http 8000
```

Ngrok te dará una URL como: `https://xxxx-xxxx-xxxx.ngrok-free.app`

Copia esta URL y actualízala en tu app Flutter en `lib/config/api_config.dart`:

```dart
static const String baseUrl = 'https://tu-url-de-ngrok.ngrok-free.app/api/v1';
```

## Paso 9: Verificar que Todo Funciona

### Checklist:

- [ ] Laravel Sanctum instalado
- [ ] Modelo User actualizado con HasApiTokens
- [ ] Migración de campos de Google ejecutada
- [ ] AuthController creado con todos los métodos
- [ ] Rutas creadas en routes/api.php
- [ ] CORS configurado
- [ ] Backend corriendo (php artisan serve)
- [ ] Ngrok corriendo (opcional, para móvil)
- [ ] Rutas probadas con cURL/Postman

### Probar desde Postman:

1. Crea una nueva colección en Postman
2. Agrega estos requests:

**POST** `http://localhost:8000/api/v1/auth/register`
```json
{
  "name": "Test User",
  "email": "test@test.com",
  "password": "12345678",
  "password_confirmation": "12345678",
  "device_id": "test-device-123"
}
```

**POST** `http://localhost:8000/api/v1/auth/login`
```json
{
  "email": "test@test.com",
  "password": "12345678",
  "device_id": "test-device-123"
}
```

**GET** `http://localhost:8000/api/v1/auth/me`
Headers:
```
Authorization: Bearer {token-obtenido-del-login}
```

## Troubleshooting

### Error: "Route [login] not defined"
Solución: Laravel Sanctum requiere que definas una ruta de login. Agrega en `routes/web.php`:
```php
Route::get('/login', function() {
    return response()->json(['message' => 'Unauthorized'], 401);
})->name('login');
```

### Error: "CORS policy"
Solución: Verifica que el middleware CORS esté habilitado en `app/Http/Kernel.php`:
```php
protected $middleware = [
    // ...
    \Illuminate\Http\Middleware\HandleCors::class,
];
```

### Error: "Unauthenticated"
Solución: Asegúrate de enviar el token en el header:
```
Authorization: Bearer tu-token-aquí
```

## Próximo Paso

Una vez que el backend esté funcionando, continúa con la configuración de Firebase para Google Sign-In en la guía `GUIA_FIREBASE_PASO_A_PASO.md`.
