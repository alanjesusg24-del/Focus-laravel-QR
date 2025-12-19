<?php

/**
 * Company: CETAM
 * Project: FF
 * File: MobileAuthController.php
 * Created on: 04/11/2025
 * Created by: Dafne Vanessa Castillo Moreno
 * Approved by: Alan Jesus Garcia Nava
 *
 * Changelog:
 * - ID: 1 | Modified on: 15/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreno
 *   Description: Refactored to comply with CETAM|
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    /**
     * Registro de usuario con email y contraseña
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:12|confirmed',
            'device_id' => 'required|string',
        ]);

        // 5.4.1: Early Return - Validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos invalidos',
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
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_id' => 'required|string',
        ]);

        // 5.4.1: Early Return - Validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos invalidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // 5.4.1: Early Return - Invalid credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        // 5.4.1: Early Return - Device mismatch
        if ($user->device_id && $user->device_id !== $request->device_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuario esta registrado en otro dispositivo',
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
    public function loginWithGoogle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'google_id' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'device_id' => 'required|string',
            'id_token' => 'required|string',
        ]);

        // 5.4.1: Early Return - Validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos invalidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            // 5.4.1: Eliminar Pirámide de la Muerte - Extract user creation/update to methods
            if (!$user) {
                $user = $this->createGoogleUser($request);
            } else {
                $deviceCheckResponse = $this->updateExistingGoogleUser($user, $request);

                // 5.4.1: Early Return - Device conflict
                if ($deviceCheckResponse) {
                    return $deviceCheckResponse;
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
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesion cerrada exitosamente',
        ]);
    }

    /**
     * Verificar email (placeholder)
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        // Implementar logica de verificacion de email
        return response()->json([
            'success' => true,
            'message' => 'Email verificado',
        ]);
    }

    /**
     * Reenviar codigo de verificacion (placeholder)
     */
    public function resendVerification(Request $request): JsonResponse
    {
        // Implementar logica de reenvio
        return response()->json([
            'success' => true,
            'message' => 'Codigo reenviado',
        ]);
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|max:12|confirmed',
        ]);

        // 5.4.1: Early Return - Validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos invalidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // 5.4.1: Early Return - Invalid current password
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
     * Olvide mi contraseña (placeholder)
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        // Implementar logica de recuperacion
        return response()->json([
            'success' => true,
            'message' => 'Codigo de recuperacion enviado',
        ]);
    }

    /**
     * Resetear contraseña (placeholder)
     */
    public function resetPassword(Request $request): JsonResponse
    {
        // Implementar logica de reset
        return response()->json([
            'success' => true,
            'message' => 'Contraseña reseteada',
        ]);
    }

    /**
     * Create new Google user
     * 5.5: Private helper method following SRP
     */
    private function createGoogleUser(Request $request): User
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'google_id' => $request->google_id,
            'profile_photo_url' => $request->profile_photo_url,
            'device_id' => $request->device_id,
            'email_verified_at' => now(), // Google ya verifico el email
        ]);
    }

    /**
     * Update existing Google user and validate device
     * 5.5: Private helper method following SRP
     *
     * @return JsonResponse|null Returns JsonResponse on device conflict, null on success
     */
    private function updateExistingGoogleUser(User $user, Request $request): ?JsonResponse
    {
        // Actualizar google_id si es necesario
        if (!$user->google_id) {
            $user->update([
                'google_id' => $request->google_id,
                'profile_photo_url' => $request->profile_photo_url,
            ]);
        }

        // 5.4.1: Early Return - Device conflict
        if ($user->device_id && $user->device_id !== $request->device_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuario esta registrado en otro dispositivo',
                'requires_device_change' => true,
                'user_id' => $user->id,
            ], 403);
        }

        // Actualizar device_id si no existe
        if (!$user->device_id) {
            $user->update(['device_id' => $request->device_id]);
        }

        return null; // Success - continue in parent method
    }
}
