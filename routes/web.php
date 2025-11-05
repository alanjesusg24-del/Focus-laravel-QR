<?php

use App\Livewire\BootstrapTables;
use App\Livewire\Components\Buttons;
use App\Livewire\Components\Forms;
use App\Livewire\Components\Modals;
use App\Livewire\Components\Notifications;
use App\Livewire\Components\Typography;
use App\Livewire\Dashboard;
use App\Livewire\Err404;
use App\Livewire\Err500;
use App\Livewire\ResetPassword;
use App\Livewire\ForgotPassword;
use App\Livewire\Lock;
use App\Livewire\Auth\Login;
use App\Livewire\Profile;
use App\Livewire\Auth\Register;
use App\Livewire\ForgotPasswordExample;
use App\Livewire\Index;
use App\Livewire\LoginExample;
use App\Livewire\ProfileExample;
use App\Livewire\RegisterExample;
use App\Livewire\Transactions;
use Illuminate\Support\Facades\Route;
use App\Livewire\ResetPasswordExample;
use App\Livewire\UpgradeToPro;
use App\Livewire\Users;

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

// Prefijo configurable del proyecto: /p/<slug>
$slug = config('proj.slug');
$namePrefix = config('proj.route_name_prefix', 'proj');

// Redirección base a login dentro del prefijo
Route::redirect('/', "/p/{$slug}/login");

Route::prefix("p/{$slug}")
    ->as($namePrefix . '.')
    ->group(function () use ($namePrefix) {
        // Público
        Route::get('/register', Register::class)->name('auth.register');
        Route::get('/login', Login::class)->name('auth.login');
        Route::get('/forgot-password', ForgotPassword::class)->name('auth.forgot-password');
        Route::get('/reset-password/{id}', ResetPassword::class)->name('auth.reset-password')->middleware('signed');

        // Errores y páginas informativas
        Route::get('/404', Err404::class)->name('errors.404');
        Route::get('/500', Err500::class)->name('errors.500');
        Route::get('/upgrade-to-pro', UpgradeToPro::class)->name('marketing.upgrade-to-pro');

        // Privado
        Route::middleware('auth')->group(function () {
            Route::get('/dashboard', Dashboard::class)->name('dashboard.index');
            Route::get('/profile', Profile::class)->name('profile.index');
            Route::get('/profile-example', ProfileExample::class)->name('profile.example');
            Route::get('/users', Users::class)->name('users.index');
            Route::get('/login-example', LoginExample::class)->name('examples.login');
            Route::get('/register-example', RegisterExample::class)->name('examples.register');
            Route::get('/forgot-password-example', ForgotPasswordExample::class)->name('examples.forgot-password');
            Route::get('/reset-password-example', ResetPasswordExample::class)->name('examples.reset-password');
            Route::get('/transactions', Transactions::class)->name('billing.transactions');
            Route::get('/bootstrap-tables', BootstrapTables::class)->name('ui.bootstrap-tables');
            Route::get('/lock', Lock::class)->name('auth.lock');
            Route::get('/buttons', Buttons::class)->name('ui.buttons');
            Route::get('/notifications', Notifications::class)->name('ui.notifications');
            Route::get('/forms', Forms::class)->name('ui.forms');
            Route::get('/modals', Modals::class)->name('ui.modals');
            Route::get('/typography', Typography::class)->name('ui.typography');
        });
    });

/*
|--------------------------------------------------------------------------
| Order QR System Routes
|--------------------------------------------------------------------------
|
| Rutas para el sistema de gestión de órdenes con QR siguiendo
| estándares CETAM - Centro de Desarrollo Tecnológico Aplicado de México
|
*/

// Authentication routes (public)
Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Public routes
Route::group(['prefix' => 'order-qr', 'as' => 'order-qr.'], function () {
    // Business Registration
    Route::get('/register', [App\Http\Controllers\BusinessController::class, 'register'])->name('business.register');
    Route::post('/register', [App\Http\Controllers\BusinessController::class, 'store'])->name('business.store');
});

// Authenticated routes - Business Dashboard
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/analytics', [App\Http\Controllers\DashboardController::class, 'analytics'])->name('dashboard.analytics');

    // Chat (only for businesses with chat module enabled)
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages/{order}', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send/{order}', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');

    // Orders Management
    Route::resource('orders', App\Http\Controllers\OrderController::class);
    Route::post('/orders/{order}/mark-ready', [App\Http\Controllers\OrderController::class, 'markAsReady'])->name('orders.markAsReady');
    Route::post('/orders/{order}/mark-delivered', [App\Http\Controllers\OrderController::class, 'markAsDelivered'])->name('orders.markAsDelivered');
    Route::post('/orders/{order}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/download-qr', [App\Http\Controllers\OrderController::class, 'downloadQr'])->name('orders.downloadQr');
    Route::get('/orders-statistics', [App\Http\Controllers\OrderController::class, 'statistics'])->name('orders.statistics');

    // Payments Management
    Route::prefix('payment')->as('order-qr.payment.')->group(function () {
        Route::get('/', [App\Http\Controllers\PaymentController::class, 'index'])->name('index');
        Route::get('/plans/{plan}/checkout', [App\Http\Controllers\PaymentController::class, 'create'])->name('checkout');
        Route::post('/plans/{plan}/checkout-session', [App\Http\Controllers\PaymentController::class, 'createCheckoutSession'])->name('create-checkout-session');
        Route::get('/success', [App\Http\Controllers\PaymentController::class, 'success'])->name('success');
        Route::get('/cancel', [App\Http\Controllers\PaymentController::class, 'cancel'])->name('cancel');
        Route::get('/history', [App\Http\Controllers\PaymentController::class, 'history'])->name('history');
        Route::delete('/subscription/cancel', [App\Http\Controllers\PaymentController::class, 'cancelSubscription'])->name('cancel-subscription');
        Route::get('/statistics', [App\Http\Controllers\PaymentController::class, 'statistics'])->name('statistics');
    });

    // Support Tickets
    Route::resource('support', App\Http\Controllers\SupportTicketController::class)->parameters([
        'support' => 'supportTicket'
    ]);
    Route::post('/support/{supportTicket}/close', [App\Http\Controllers\SupportTicketController::class, 'close'])->name('support.close');
    Route::post('/support/{supportTicket}/reopen', [App\Http\Controllers\SupportTicketController::class, 'reopen'])->name('support.reopen');

    // Business Profile Management
    Route::get('/business/profile', [App\Http\Controllers\BusinessController::class, 'profile'])->name('business.profile');
    Route::get('/business/edit', [App\Http\Controllers\BusinessController::class, 'edit'])->name('business.edit');
    Route::put('/business/update', [App\Http\Controllers\BusinessController::class, 'update'])->name('business.update');
    Route::get('/business/change-password', [App\Http\Controllers\BusinessController::class, 'showChangePassword'])->name('business.changePassword');
    Route::put('/business/password', [App\Http\Controllers\BusinessController::class, 'updatePassword'])->name('business.updatePassword');
    // Theme routes removed - CETAM institutional standards must be maintained
    // Route::get('/business/theme', [App\Http\Controllers\BusinessController::class, 'showTheme'])->name('business.theme');
    // Route::put('/business/theme', [App\Http\Controllers\BusinessController::class, 'updateTheme'])->name('business.updateTheme');
    Route::post('/business/deactivate', [App\Http\Controllers\BusinessController::class, 'deactivate'])->name('business.deactivate');
});

// Public webhook endpoint (no auth required)
Route::post('/webhook/stripe', [App\Http\Controllers\PaymentController::class, 'webhook'])->name('webhook.stripe');
