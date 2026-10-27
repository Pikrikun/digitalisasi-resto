<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Resto\DashboardController;
use App\Http\Controllers\Resto\MenuController;
use App\Http\Controllers\Resto\TableController;
use App\Http\Controllers\Resto\OrderController;
use App\Http\Controllers\Resto\SettingsController;
use App\Http\Controllers\PublicOrderController;
use App\Console\Commands\CancelExpiredOrders;
use Illuminate\Support\Facades\Schedule;


// API untuk cek ketersediaan menu secara real-time
Route::get('/api/check-menu-status/{slug}', function ($slug) {
    $restaurant = \App\Models\Restaurant::where('slug', $slug)->firstOrFail();
    
    // Ambil semua ID produk yang saat ini tersedia
    $availableIds = \App\Models\Product::where('restaurant_id', $restaurant->id)
        ->where('is_available', true)
        ->pluck('id')
        ->toArray();
        
    return response()->json(['available_ids' => $availableIds]);
});


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('resto.dashboard');
    }
    return view('landing');
})->name('landing');

// Halaman Pelanggan (Scan QR)
Route::get('/r/{slug}/meja/{token}', [PublicOrderController::class, 'showMenu'])->name('public.menu');
Route::post('/r/{slug}/meja/{token}/order', [PublicOrderController::class, 'submitOrder'])->name('public.order');

// Halaman Lacak Pesanan (Pakai Token Acak)
Route::get('/track/{token}', [PublicOrderController::class, 'trackOrderSimpel'])->name('public.track');
Route::post('/track/{order}/upload', [PublicOrderController::class, 'reuploadProof'])->name('public.reupload');

// API Cek Status Pesanan (Untuk pelanggan)
Route::get('/api/check-order-status/{order}', function (\App\Models\Order $order) {
    return response()->json(['status' => $order->status]);
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Lupa Password via OTP
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
    Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify-form');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| DASHBOARD RESTO ROUTES (Wajib Login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('resto')->name('resto.')->group(function () {
    Route::get('/dashboard/live-stats', [DashboardController::class, 'getLiveStats'])->name('dashboard.live');
    
    // 1. Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/download-report', [DashboardController::class, 'downloadReport'])->name('dashboard.report');

    // 2. Kelola Menu
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{product}/edit', [MenuController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{product}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{product}', [MenuController::class, 'destroy'])->name('menu.destroy');
    Route::patch('/menu/{product}/toggle', [MenuController::class, 'toggleAvailable'])->name('menu.toggle');

    // 3. Kategori
    Route::post('/categories', [MenuController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [MenuController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [MenuController::class, 'destroyCategory'])->name('categories.destroy');

    // 4. Cetak QR Meja
    Route::get('/table', [TableController::class, 'index'])->name('table.index');
    Route::post('/table', [TableController::class, 'store'])->name('table.store');
    Route::delete('/table/{table}', [TableController::class, 'destroy'])->name('table.destroy');
    Route::get('/table/{table}/print', [TableController::class, 'print'])->name('table.print');

    // 5. Riwayat Pesanan & Verifikasi
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/live', [OrderController::class, 'getLiveOrders'])->name('orders.live');
    Route::get('/orders/partial-cards', [OrderController::class, 'getPartialCards'])->name('orders.partial');
    Route::get('/orders/{order}/detail', [OrderController::class, 'getDetail'])->name('orders.detail');
    Route::post('/orders/{order}/verify', [OrderController::class, 'verifyPayment'])->name('orders.verify');
    Route::get('/orders/{order}/print', [OrderController::class, 'printReceipt'])->name('orders.print');

    // 6. Pengaturan Toko
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/qris', [SettingsController::class, 'updateQris'])->name('settings.qris');
    Route::post('/settings/send-pin-otp', [SettingsController::class, 'sendPinOtp'])->name('settings.sendPinOtp');
    Route::post('/settings/reset-pin', [SettingsController::class, 'resetPin'])->name('settings.resetPin');

    // 7. API Global
    Route::get('/api/check-new-orders', [OrderController::class, 'checkNewOrders'])->name('orders.check');
    Route::get('/api/live-orders', [OrderController::class, 'getLiveOrders'])->name('orders.live.api');
});

/*
|--------------------------------------------------------------------------
| SCHEDULE (Auto Cancel)
|--------------------------------------------------------------------------
*/
Schedule::command(CancelExpiredOrders::class)->everyMinute();