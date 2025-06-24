<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        // Buscar producto por ID con categoría y marca cargadas
        $product = Product::with(['category', 'brand', 'productPhotos'])->findOrFail($id);

        return view('products.show', compact('product'));
    }
}
