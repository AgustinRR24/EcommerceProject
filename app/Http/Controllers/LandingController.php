<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Categorie;
use App\Models\Brand;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        // Filtro por precio
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filtro por categorías
        if ($request->filled('categories')) {
            $query->whereIn('category_id', $request->categories);
        }

        // Filtro por marcas
        if ($request->filled('brands')) {
            $query->whereIn('brand_id', $request->brands);
        }

        $products = $query->where('is_active', true)->paginate(9)->withQueryString();
        $categories = Categorie::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        // Para mostrar la imagen principal del producto
        foreach ($products as $product) {
            $product->image_url = $product->image 
                ? asset('storage/' . $product->image)
                : ($product->productPhotos->first()->url_photo ?? null);
        }

        return view('landing', compact('products', 'categories', 'brands'));
    }
}