<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
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
                'message' => 'Datos invalidos',
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
                'message' => 'Datos invalidos',
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
                    'email_verified_at' => now(), // Google ya verifico el email
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
                        'message' => 'Este usuario esta registrado en otro dispositivo',
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
            'message' => 'Sesion cerrada exitosamente',
        ]);
    }

    /**
     * Verificar email (placeholder)
     */
    public function verifyEmail(Request $request)
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
    public function resendVerification(Request $request)
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
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos invalidos',
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
     * Olvide mi contraseña (placeholder)
     */
    public function forgotPassword(Request $request)
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
    public function resetPassword(Request $request)
    {
        // Implementar logica de reset
        return response()->json([
            'success' => true,
            'message' => 'Contraseña reseteada',
        ]);
    }
}
