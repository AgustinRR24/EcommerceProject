<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show($id)
    {
        // Buscar producto por ID con categoría y marca cargadas
        $product = Product::with(['category', 'brand', 'productPhotos'])->findOrFail($id);

        return Inertia::render('ProductShow', [
            'product' => $product
        ]);
    }
}
