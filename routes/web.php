<?php

/**
 * ============================================
 * CETAM - Web Routes
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        web.php
 * @description Rutas web del sistema Order QR
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

use App\Livewire\Auth\RegisterWizard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Rutas de Volt Dashboard - ELIMINADAS
|--------------------------------------------------------------------------
| Las rutas de demostración de Volt Dashboard han sido removidas.
| El sistema usa exclusivamente las rutas de Business y SuperAdmin.
*/

/*
|--------------------------------------------------------------------------
| Order QR System Routes
|--------------------------------------------------------------------------
|
| Rutas para el sistema de gestión de órdenes con QR siguiendo
| estándares CETAM - Centro de Desarrollo Tecnológico Aplicado de México
|
*/

// Redirect root to business login
Route::get('/', function () {
    return redirect()->route('business.login');
});

// Business routes with 'business.' prefix
Route::group(['prefix' => 'business', 'as' => 'business.'], function () {

    // Public authentication routes
    Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');
    Route::get('/register', RegisterWizard::class)->name('register');
    Route::post('/register', [App\Http\Controllers\Business\BusinessController::class, 'store']);

    // Payments Management (NO requiere subscription activa para renovar)
    Route::middleware(['auth:business'])->group(function () {
        Route::prefix('payments')->as('payments.')->group(function () {
            Route::get('/', [App\Http\Controllers\Payments\PaymentController::class, 'index'])->name('index');
            Route::get('/plans/{plan}/checkout', [App\Http\Controllers\Payments\PaymentController::class, 'create'])->name('checkout');
            Route::post('/plans/{plan}/checkout-session', [App\Http\Controllers\Payments\PaymentController::class, 'createCheckoutSession'])->name('create-checkout-session');
            Route::get('/success', [App\Http\Controllers\Payments\PaymentController::class, 'success'])->name('success');
            Route::get('/cancel', [App\Http\Controllers\Payments\PaymentController::class, 'cancel'])->name('cancel');
            Route::get('/history', [App\Http\Controllers\Payments\PaymentController::class, 'history'])->name('history');
            Route::delete('/subscription/cancel', [App\Http\Controllers\Payments\PaymentController::class, 'cancelSubscription'])->name('cancel-subscription');
            Route::get('/statistics', [App\Http\Controllers\Payments\PaymentController::class, 'statistics'])->name('statistics');
        });
    });

    // Authenticated routes (using business guard) with subscription check
    Route::middleware(['auth:business', 'subscription.active'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\General\DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/analytics', [App\Http\Controllers\General\DashboardController::class, 'analytics'])->name('dashboard.analytics');

        // Chat (only for businesses with chat module enabled)
        Route::get('/chat', [App\Http\Controllers\Chat\ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/messages/{order}', [App\Http\Controllers\Chat\ChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/chat/send/{order}', [App\Http\Controllers\Chat\ChatController::class, 'sendMessage'])->name('chat.send');

        // Orders Management
        Route::prefix('orders')->as('orders.')->group(function () {
            Route::get('/', [App\Http\Controllers\Orders\OrderController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Orders\OrderController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Orders\OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [App\Http\Controllers\Orders\OrderController::class, 'show'])->name('show');
            Route::get('/{order}/edit', [App\Http\Controllers\Orders\OrderController::class, 'edit'])->name('edit');
            Route::put('/{order}', [App\Http\Controllers\Orders\OrderController::class, 'update'])->name('update');
            Route::delete('/{order}', [App\Http\Controllers\Orders\OrderController::class, 'destroy'])->name('destroy');
            Route::put('/{order}/mark-ready', [App\Http\Controllers\Orders\OrderController::class, 'markAsReady'])->name('markAsReady');
            Route::put('/{order}/mark-delivered', [App\Http\Controllers\Orders\OrderController::class, 'markAsDelivered'])->name('markAsDelivered');
            Route::put('/{order}/cancel', [App\Http\Controllers\Orders\OrderController::class, 'cancel'])->name('cancel');
            Route::get('/{order}/download-qr', [App\Http\Controllers\Orders\OrderController::class, 'downloadQr'])->name('downloadQr');
            Route::get('/{order}/check-linked', [App\Http\Controllers\Orders\OrderController::class, 'checkLinked'])->name('checkLinked');
        });
        Route::get('/orders-statistics', [App\Http\Controllers\Orders\OrderController::class, 'statistics'])->name('orders.statistics');

        // Support Tickets
        Route::prefix('support')->as('support.')->group(function () {
            Route::get('/', [App\Http\Controllers\Support\SupportTicketController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Support\SupportTicketController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Support\SupportTicketController::class, 'store'])->name('store');
            Route::get('/{supportTicket}', [App\Http\Controllers\Support\SupportTicketController::class, 'show'])->name('show');
            Route::get('/{supportTicket}/edit', [App\Http\Controllers\Support\SupportTicketController::class, 'edit'])->name('edit');
            Route::put('/{supportTicket}', [App\Http\Controllers\Support\SupportTicketController::class, 'update'])->name('update');
            Route::delete('/{supportTicket}', [App\Http\Controllers\Support\SupportTicketController::class, 'destroy'])->name('destroy');
            Route::post('/{supportTicket}/close', [App\Http\Controllers\Support\SupportTicketController::class, 'close'])->name('close');
            Route::post('/{supportTicket}/reopen', [App\Http\Controllers\Support\SupportTicketController::class, 'reopen'])->name('reopen');
        });

        // Business Profile Management
        Route::get('/profile', [App\Http\Controllers\Business\BusinessController::class, 'profile'])->name('profile.index');
        Route::get('/profile/edit', [App\Http\Controllers\Business\BusinessController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Business\BusinessController::class, 'update'])->name('profile.update');
        Route::get('/profile/change-password', [App\Http\Controllers\Business\BusinessController::class, 'showChangePassword'])->name('profile.change-password');
        Route::put('/profile/password', [App\Http\Controllers\Business\BusinessController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/deactivate', [App\Http\Controllers\Business\BusinessController::class, 'deactivate'])->name('profile.deactivate');
    });
});

// Public webhook endpoints (no auth required)
Route::post('/webhook/stripe', [App\Http\Controllers\Payments\PaymentController::class, 'webhook'])->name('webhook.stripe');
// Mercado Pago DESHABILITADO
// Route::post('/webhook/mercadopago', [App\Http\Controllers\Payments\MercadoPagoWebhookController::class, 'handleWebhook'])->name('webhook.mercadopago');

// Test QR Scanner
Route::get('/test-scanner', function() {
    return view('test-scanner');
})->name('test.scanner');

// Mobile App Configuration QR
Route::get('/mobile-config', function() {
    $apiUrl = config('app.url') . '/api';
    $isNgrok = str_contains(config('app.url'), 'ngrok');
    $serverType = $isNgrok ? 'ngrok' : 'local';

    return view('mobile-config', compact('apiUrl', 'isNgrok', 'serverType'));
})->name('mobile.config');

// API endpoint to get current server URL (for mobile app auto-discovery)
Route::get('/api/server-info', function() {
    return response()->json([
        'api_url' => config('app.url') . '/api',
        'app_url' => config('app.url'),
        'server_type' => str_contains(config('app.url'), 'ngrok') ? 'ngrok' : 'local',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('api.server-info');

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
| Rutas para el panel de superadministrador
|
*/

Route::prefix('superadmin')->as('superadmin.')->group(function () {
    // Public routes
    Route::get('/login', [App\Http\Controllers\SuperAdmin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\SuperAdmin\AuthController::class, 'login']);

    // Protected routes
    Route::middleware(['auth:superadmin'])->group(function () {
        Route::post('/logout', [App\Http\Controllers\SuperAdmin\AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

        // Businesses Management
        Route::prefix('businesses')->as('businesses.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\BusinessManagementController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\SuperAdmin\BusinessManagementController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [App\Http\Controllers\SuperAdmin\BusinessManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\SuperAdmin\BusinessManagementController::class, 'update'])->name('update');
            Route::post('/{id}/toggle', [App\Http\Controllers\SuperAdmin\BusinessManagementController::class, 'toggleStatus'])->name('toggle');
            Route::delete('/{id}', [App\Http\Controllers\SuperAdmin\BusinessManagementController::class, 'destroy'])->name('destroy');
        });

        // Plans Management
        Route::prefix('plans')->as('plans.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\SuperAdmin\PlanManagementController::class, 'destroy'])->name('destroy');
        });

        // Global Orders (not needed - each business manages their own orders)
        // Route::get('/orders', [App\Http\Controllers\SuperAdmin\GlobalOrderController::class, 'index'])->name('orders.index');

        // Payments Management
        Route::get('/payments', [App\Http\Controllers\SuperAdmin\PaymentManagementController::class, 'index'])->name('payments.index');

        // Support Tickets
        Route::get('/tickets', [App\Http\Controllers\SuperAdmin\TicketManagementController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [App\Http\Controllers\SuperAdmin\TicketManagementController::class, 'show'])->name('tickets.show');
        Route::get('/tickets/{ticket}/respond', [App\Http\Controllers\SuperAdmin\TicketManagementController::class, 'respond'])->name('tickets.respond');
        Route::post('/tickets/{ticket}/respond', [App\Http\Controllers\SuperAdmin\TicketManagementController::class, 'storeResponse'])->name('tickets.storeResponse');
        Route::patch('/tickets/{ticket}/status', [App\Http\Controllers\SuperAdmin\TicketManagementController::class, 'updateStatus'])->name('tickets.updateStatus');

        // Reports (integrated into Dashboard)
        // Route::get('/reports', [App\Http\Controllers\SuperAdmin\ReportController::class, 'index'])->name('reports.index');

        // Profile Management
        Route::get('/profile', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/edit', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'update'])->name('profile.update');
    });
});
Route::get('/test-icons', function() { return view('test-icons'); });
