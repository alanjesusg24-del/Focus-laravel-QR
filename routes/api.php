<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\MobileOrderController;
// use App\Http\Controllers\Api\MobileDeviceController;
// use App\Http\Controllers\Api\MobileAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Order QR System - Mobile API Routes
|--------------------------------------------------------------------------
|
| Rutas API para la aplicación móvil del sistema de órdenes con QR
| Todas las rutas utilizan versioning: /api/v1/...
|
*/

Route::prefix('v1')->group(function () {

    // TODO: Implement Mobile API Controllers
    // Public endpoints (no authentication required)
    // Route::post('/mobile/register', [MobileAuthController::class, 'register']);
    // Route::post('/mobile/login', [MobileAuthController::class, 'login']);
    // Route::post('/mobile/forgot-password', [MobileAuthController::class, 'forgotPassword']);

    // QR Code Validation (public - para escanear QR sin autenticación previa)
    // Route::post('/qr/validate', [MobileOrderController::class, 'validateQrCode']);
    // Route::post('/qr/link', [MobileOrderController::class, 'linkOrderToUser']);

    // Protected endpoints (require mobile user authentication)
    // Route::middleware(['auth:sanctum'])->group(function () {

        // Mobile User Profile
        // Route::get('/mobile/profile', [MobileAuthController::class, 'profile']);
        // Route::put('/mobile/profile', [MobileAuthController::class, 'updateProfile']);
        // Route::post('/mobile/logout', [MobileAuthController::class, 'logout']);
        // Route::put('/mobile/change-password', [MobileAuthController::class, 'changePassword']);

        // Mobile Device Management (FCM Tokens)
        // Route::post('/mobile/devices/register', [MobileDeviceController::class, 'registerDevice']);
        // Route::delete('/mobile/devices/{fcmToken}', [MobileDeviceController::class, 'unregisterDevice']);
        // Route::get('/mobile/devices', [MobileDeviceController::class, 'listDevices']);

        // Orders for Mobile User
        // Route::get('/mobile/orders', [MobileOrderController::class, 'index']); // Mis órdenes
        // Route::get('/mobile/orders/{order}', [MobileOrderController::class, 'show']); // Detalle
        // Route::get('/mobile/orders/{order}/qr', [MobileOrderController::class, 'getQrCode']); // Obtener QR
        // Route::post('/mobile/orders/{order}/confirm-pickup', [MobileOrderController::class, 'confirmPickup']); // Confirmar recogida

        // Notifications
        // Route::get('/mobile/notifications', [MobileOrderController::class, 'getNotifications']);
        // Route::post('/mobile/notifications/{notification}/read', [MobileOrderController::class, 'markNotificationAsRead']);
        // Route::post('/mobile/notifications/read-all', [MobileOrderController::class, 'markAllNotificationsAsRead']);

        // Business Search (para encontrar negocios cercanos)
        // Route::get('/mobile/businesses/search', [MobileOrderController::class, 'searchBusinesses']);
        // Route::get('/mobile/businesses/{business}', [MobileOrderController::class, 'getBusinessDetails']);
    // });

    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'Order QR System API',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // API Documentation endpoint
    Route::get('/docs', function () {
        return response()->json([
            'message' => 'Order QR System API v1',
            'documentation' => url('/api/documentation'),
            'endpoints' => [
                'authentication' => [
                    'POST /api/v1/mobile/register',
                    'POST /api/v1/mobile/login',
                    'POST /api/v1/mobile/logout',
                ],
                'orders' => [
                    'GET /api/v1/mobile/orders',
                    'GET /api/v1/mobile/orders/{id}',
                    'POST /api/v1/qr/validate',
                    'POST /api/v1/qr/link',
                ],
                'devices' => [
                    'POST /api/v1/mobile/devices/register',
                    'DELETE /api/v1/mobile/devices/{token}',
                ],
                'notifications' => [
                    'GET /api/v1/mobile/notifications',
                    'POST /api/v1/mobile/notifications/{id}/read',
                ],
            ],
        ]);
    });
});
