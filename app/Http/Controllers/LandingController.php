<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Categorie;
use App\Models\Brand;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'productPhotos']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

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

        $products = $query->where('is_active', true)->paginate(12)->withQueryString();
        $categories = Categorie::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return Inertia::render('Landing', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->only(['min_price', 'max_price', 'categories', 'brands', 'search'])
        ]);
    }
}