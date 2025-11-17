<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', [LandingController::class, 'home'])->name('home');
Route::get('/products', [LandingController::class, 'index'])->name('landing');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Rutas del carrito
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'getCount'])->name('count');
    Route::post('/update-prices', [CartController::class, 'updateCartPrices'])->name('update-prices');
});

// Rutas del checkout
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::post('/validate-promo', [CheckoutController::class, 'validatePromoCode'])->name('validate-promo');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
    Route::get('/failure', [CheckoutController::class, 'failure'])->name('failure');
    Route::get('/pending', [CheckoutController::class, 'pending'])->name('pending');
});

// Webhook sin CSRF para MercadoPago
Route::post('/checkout/webhook', [CheckoutController::class, 'webhook'])->name('checkout.webhook')->withoutMiddleware(['csrf']);

// Endpoint manual para procesar pagos (útil para desarrollo)
Route::post('/checkout/process-payment', [CheckoutController::class, 'processPayment'])->name('checkout.process-payment')->withoutMiddleware(['csrf']);
Route::get('/checkout/process-payment-test/{paymentId}/{externalReference}', function($paymentId, $externalReference) {
    $controller = new \App\Http\Controllers\CheckoutController();
    $request = new \Illuminate\Http\Request(['payment_id' => $paymentId, 'external_reference' => $externalReference]);
    $service = app(\App\Services\MercadoPagoService::class);
    return $controller->processPayment($request, $service);
})->name('checkout.process-payment-test');

// Endpoint para verificar pagos pendientes automáticamente
Route::get('/checkout/check-pending', [CheckoutController::class, 'checkPendingPayments'])->name('checkout.check-pending');

// Ruta para imprimir órdenes
Route::get('/order/{order}/print', function(\App\Models\Order $order) {
    return view('order-invoice', ['record' => $order]);
})->name('order.print');

// Nuevas rutas para páginas adicionales
Route::get('/hotsale', [\App\Http\Controllers\LandingController::class, 'hotsale'])->name('hotsale');
Route::get('/about', [\App\Http\Controllers\LandingController::class, 'about'])->name('about');

// Páginas legales
Route::get('/terms', [\App\Http\Controllers\LegalController::class, 'terms'])->name('terms');
Route::get('/privacy', [\App\Http\Controllers\LegalController::class, 'privacy'])->name('privacy');
Route::get('/legal', [\App\Http\Controllers\LegalController::class, 'legal'])->name('legal');
