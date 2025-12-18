<?php

/**
 * Company: CETAM
 * Project: FF
 * File: AuthController.php (API V1)
 * Created on: 20/10/2025
 * Created by: Dafne Vanessa Castillo Moreo
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 15/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreo |
 *   Description: Refactored to comply  |
 */

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    public function register(Request $request): JsonResponse
    {
        Log::info('=== INICIO REGISTRO ===');
        Log::info('Email recibido: ' . $request->email);
        Log::info('Device ID recibido: ' . ($request->device_id ?? 'NULL'));

        // device_id es OPCIONAL
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:mobile_users,email',
            'password' => 'required|string|min:6',
            'device_id' => 'nullable|string',
            'fcm_token' => 'nullable|string',
            'platform' => 'nullable|string|in:ios,android',
        ]);

        if ($validator->fails()) {
            Log::error('Validación falló:', $validator->errors()->toArray());

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = MobileUser::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'device_id' => $request->device_id,
                'email_verified_at' => now(),
            ]);

            Log::info('Usuario creado exitosamente: ' . $user->email);

            // 5.5: Register FCM token if provided
            $this->registerFcmToken($user->id, $request);

            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'token' => $token,
                'user' => $this->formatUserResponse($user),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear usuario: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login of user
     */
    public function login(Request $request): JsonResponse
    {
        Log::info('=== INICIO LOGIN ===');
        Log::info('Email recibido: ' . $request->email);
        Log::info('Device ID recibido: ' . ($request->device_id ?? 'NULL'));

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_id' => 'nullable|string',
            'fcm_token' => 'nullable|string',
            'platform' => 'nullable|string|in:ios,android',
        ]);

        if ($validator->fails()) {
            Log::error('Validación de login falló:', $validator->errors()->toArray());

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = MobileUser::where('email', $request->email)->first();

        // 5.4.1: Early Return - Invalid credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning('Credenciales incorrectas para: ' . $request->email);

            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

 
        if ($request->device_id) {
            $user->device_id = $request->device_id;
            $user->save();
        }

        // 5.5: Register FCM token if provided
        $this->registerFcmToken($user->id, $request);

        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        Log::info('Login exitoso para: ' . $user->email);

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => $this->formatUserResponse($user),
        ], 200);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => $this->formatUserResponse($user),
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente',
        ], 200);
    }

    public function requestDeviceChange(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'new_device_id' => 'required|string',
        ]);

        // 5.4.1: Early Return - Validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = MobileUser::where('email', $request->email)->first();

        // 5.4.1: Early Return - Invalid credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        $user->device_id = $request->new_device_id;
        $user->save();

        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Dispositivo cambiado exitosamente',
            'token' => $token,
            'user' => $this->formatUserResponse($user),
        ], 200);
    }

    public function verifyDeviceChange(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Función no implementada - usar requestDeviceChange',
        ], 501);
    }

    
    //Register FCM token for push notifications
     
    private function registerFcmToken(int $userId, Request $request): void
    {
        
        if (!$request->fcm_token) {
            return;
        }

        \App\Models\MobileDevice::updateOrCreate(
            [
                'mobile_user_id' => $userId,
                'fcm_token' => $request->fcm_token,
            ],
            [
                'platform' => $request->platform ?? 'android',
                'is_active' => true,
            ]
        );

        Log::info('FCM token registrado', [
            'user_id' => $userId,
        ]);
    }

    /**
     * Format user object for API response
     *  Eliminates code duplication
     */
    private function formatUserResponse(MobileUser $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'device_id' => $user->device_id,
            'email_verified' => $user->hasVerifiedEmail(),
        ];
    }
}
