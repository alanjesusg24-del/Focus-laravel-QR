<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        \Log::info('=== INICIO REGISTRO ===');
        \Log::info('Email recibido: ' . $request->email);
        \Log::info('Device ID recibido: ' . ($request->device_id ?? 'NULL'));

        // Validación - device_id es OPCIONAL
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:mobile_users,email',
            'password' => 'required|string|min:6',
            'device_id' => 'nullable|string', // OPCIONAL - puede ser null
            'fcm_token' => 'nullable|string', // Token de Firebase Cloud Messaging
            'platform' => 'nullable|string|in:ios,android', // Plataforma del dispositivo
        ]);

        if ($validator->fails()) {
            \Log::error('Validación falló:', $validator->errors()->toArray());

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // NO HAY VALIDACIÓN DE DEVICE_ID ÚNICO
        // Un mismo dispositivo puede tener múltiples cuentas

        // Crear usuario
        try {
            $user = MobileUser::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'device_id' => $request->device_id, // Puede ser null
                'email_verified_at' => now(),
            ]);

            \Log::info('Usuario creado exitosamente: ' . $user->email);

            // Crear o actualizar dispositivo con FCM token si se proporciona
            if ($request->fcm_token) {
                \App\Models\MobileDevice::updateOrCreate(
                    [
                        'mobile_user_id' => $user->id,
                        'fcm_token' => $request->fcm_token,
                    ],
                    [
                        'platform' => $request->platform ?? 'android',
                        'is_active' => true,
                    ]
                );

                \Log::info('FCM token registrado en registro', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }

            // Generar token
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'device_id' => $user->device_id,
                    'email_verified' => $user->hasVerifiedEmail(),
                ],
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login de usuario
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        \Log::info('=== INICIO LOGIN ===');
        \Log::info('Email recibido: ' . $request->email);
        \Log::info('Device ID recibido: ' . ($request->device_id ?? 'NULL'));

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_id' => 'nullable|string', // OPCIONAL
            'fcm_token' => 'nullable|string', // Token de Firebase Cloud Messaging
            'platform' => 'nullable|string|in:ios,android', // Plataforma del dispositivo
        ]);

        if ($validator->fails()) {
            \Log::error('Validación de login falló:', $validator->errors()->toArray());

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Buscar usuario por email
        $user = MobileUser::where('email', $request->email)->first();

        // Verificar credenciales
        if (!$user || !Hash::check($request->password, $user->password)) {
            \Log::warning('Credenciales incorrectas para: ' . $request->email);

            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        // NO HAY VALIDACIÓN DE DEVICE_ID
        // El usuario puede loguearse desde cualquier dispositivo

        // Actualizar device_id si se proporciona
        if ($request->device_id) {
            $user->device_id = $request->device_id;
            $user->save();
        }

        // Crear o actualizar dispositivo con FCM token si se proporciona
        if ($request->fcm_token) {
            \App\Models\MobileDevice::updateOrCreate(
                [
                    'mobile_user_id' => $user->id,
                    'fcm_token' => $request->fcm_token,
                ],
                [
                    'platform' => $request->platform ?? 'android',
                    'is_active' => true,
                ]
            );

            \Log::info('FCM token registrado en login', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        // Revocar tokens anteriores (opcional)
        $user->tokens()->delete();

        // Generar nuevo token
        $token = $user->createToken('mobile-app')->plainTextToken;

        \Log::info('Login exitoso para: ' . $user->email);

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'device_id' => $user->device_id,
                'email_verified' => $user->hasVerifiedEmail(),
            ],
        ], 200);
    }

    /**
     * Obtener información del usuario autenticado
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'device_id' => $user->device_id,
                'email_verified' => $user->hasVerifiedEmail(),
            ],
        ], 200);
    }

    /**
     * Logout
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revocar token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente',
        ], 200);
    }

    /**
     * Solicitar cambio de dispositivo
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestDeviceChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'new_device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verificar credenciales
        $user = MobileUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        // Actualizar device_id directamente (simplificado)
        // En producción, deberías implementar un sistema de verificación con código
        $user->device_id = $request->new_device_id;
        $user->save();

        // Revocar todos los tokens anteriores
        $user->tokens()->delete();

        // Generar nuevo token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Dispositivo cambiado exitosamente',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'device_id' => $user->device_id,
                'email_verified' => $user->hasVerifiedEmail(),
            ],
        ], 200);
    }

    /**
     * Verificar cambio de dispositivo (para implementación futura con códigos)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyDeviceChange(Request $request)
    {
        // TODO: Implementar sistema de códigos de verificación si es necesario
        return response()->json([
            'success' => false,
            'message' => 'Función no implementada - usar requestDeviceChange',
        ], 501);
    }
}
