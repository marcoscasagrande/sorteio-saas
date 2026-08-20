<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\GiveawayController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstagramOAuthController;
use App\Http\Controllers\MercadoPagoWebhookController;
use Illuminate\Support\Facades\Route;

// Home pública
Route::get('/', [HomeController::class, 'index'])->name('home');

// Página pública de comprovação do sorteio (link que o organizador
// compartilha com a audiência — não exige login)
Route::get('/verificar/{hash}', [GiveawayController::class, 'verify'])->name('giveaways.verify');

// Auth (visitante)
Route::middleware('guest')->group(function () {
    Route::get('/registrar', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/registrar', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

// Webhook do Mercado Pago (público, sem CSRF — ver bootstrap/app.php)
Route::post('/webhooks/mercadopago', [MercadoPagoWebhookController::class, 'handle'])
    ->name('webhooks.mercadopago');

// Área do usuário autenticado
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::get('/instagram/conectar', [InstagramOAuthController::class, 'redirect'])->name('instagram.connect');
    Route::get('/instagram/callback', [InstagramOAuthController::class, 'callback'])->name('instagram.callback');

    Route::get('/sorteios/novo', [GiveawayController::class, 'create'])->name('giveaways.create');
    Route::post('/sorteios', [GiveawayController::class, 'store'])->name('giveaways.store');
    Route::get('/sorteios/{giveaway}', [GiveawayController::class, 'show'])->name('giveaways.show');
    Route::get('/sorteios/{giveaway}/pagar', [GiveawayController::class, 'pay'])->name('giveaways.pay');
    Route::post('/sorteios/{giveaway}/sortear', [GiveawayController::class, 'draw'])->name('giveaways.draw');
});

// Área administrativa
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/usuarios', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/usuarios/{user}', [AdminUserController::class, 'show'])->name('users.show');

    Route::get('/relatorio-de-vendas', [AdminReportController::class, 'sales'])->name('reports.sales');

    Route::get('/configuracoes', [AdminSettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/configuracoes', [AdminSettingsController::class, 'update'])->name('settings.update');
});
