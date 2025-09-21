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

    public function home()
    {
        // Obtener los 6 productos más caros con stock disponible
        $topProducts = Product::with(['category', 'brand', 'productPhotos'])
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('price', 'desc')
            ->limit(6)
            ->get();

        return Inertia::render('Home', [
            'topProducts' => $topProducts
        ]);
    }

    public function hotsale(Request $request)
    {
        $query = Product::with(['category', 'brand', 'productPhotos']);

        // Solo productos con descuento (precio de descuento menor al precio regular)
        $query->whereNotNull('discount_price')
              ->whereColumn('discount_price', '<', 'price');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por precio
        if ($request->filled('min_price')) {
            $query->where('discount_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('discount_price', '<=', $request->max_price);
        }

        $products = $query->where('is_active', true)->paginate(12)->withQueryString();

        return Inertia::render('HotSale', [
            'products' => $products,
            'filters' => $request->only(['min_price', 'max_price', 'search'])
        ]);
    }

    public function about()
    {
        return Inertia::render('About');
    }
}