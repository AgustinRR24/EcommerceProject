<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProductController;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
