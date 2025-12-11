<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Categorie;
use App\Models\Brand;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'min_price' => 'nullable|numeric|min:0|max:999999',
            'max_price' => 'nullable|numeric|min:0|max:999999',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
            'brands' => 'nullable|array',
            'brands.*' => 'integer|exists:brands,id',
            'sort' => 'nullable|in:featured,price_asc,price_desc,newest',
        ]);

        $query = Product::with(['category', 'brand', 'productPhotos']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $query->where(function($q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['search'] . '%')
                  ->orWhere('description', 'like', '%' . $validated['search'] . '%');
            });
        }

        // Filtro por precio
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $validated['min_price']);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $validated['max_price']);
        }

        // Filtro por categorías
        if ($request->filled('categories')) {
            $query->whereIn('category_id', $validated['categories']);
        }

        // Filtro por marcas
        if ($request->filled('brands')) {
            $query->whereIn('brand_id', $validated['brands']);
        }

        $query->where('is_active', true);

        // Ordenamiento
        $sort = $validated['sort'] ?? 'featured';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
            default:
                // Ordenar por más vendidos (usando subconsulta)
                $query->leftJoin(DB::raw('(SELECT product_id, SUM(quantity) as total_quantity FROM order_details GROUP BY product_id) as sales'),
                    'products.id', '=', 'sales.product_id')
                    ->select('products.*')
                    ->orderByRaw('COALESCE(sales.total_quantity, 0) DESC');
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Categorie::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return Inertia::render('Landing', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->only(['min_price', 'max_price', 'categories', 'brands', 'search', 'sort'])
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
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'min_price' => 'nullable|numeric|min:0|max:999999',
            'max_price' => 'nullable|numeric|min:0|max:999999',
            'sort' => 'nullable|in:discount,price_asc,price_desc,newest',
        ]);

        $query = Product::with(['category', 'brand', 'productPhotos']);

        // Solo productos con descuento (precio de descuento menor al precio regular)
        $query->whereNotNull('discount_price')
              ->whereColumn('discount_price', '<', 'price');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $query->where(function($q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['search'] . '%')
                  ->orWhere('description', 'like', '%' . $validated['search'] . '%');
            });
        }

        // Filtro por precio
        if ($request->filled('min_price')) {
            $query->where('discount_price', '>=', $validated['min_price']);
        }
        if ($request->filled('max_price')) {
            $query->where('discount_price', '<=', $validated['max_price']);
        }

        $query->where('is_active', true);

        // Ordenamiento
        $sort = $validated['sort'] ?? 'discount';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('discount_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('discount_price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'discount':
            default:
                // Ordenar por mayor porcentaje de descuento
                $query->orderByRaw('((price - discount_price) / price * 100) DESC');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return Inertia::render('HotSale', [
            'products' => $products,
            'filters' => $request->only(['min_price', 'max_price', 'search', 'sort'])
        ]);
    }

    public function about()
    {
        return Inertia::render('About');
    }
}