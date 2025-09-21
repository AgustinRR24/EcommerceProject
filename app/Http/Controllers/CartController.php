<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/customer/login')->with('message', 'Debes iniciar sesión para ver tu carrito');
        }

        $userId = Auth::id();

        $cartItems = CartItem::with('product')
            ->forUser($userId)
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return Inertia::render('Cart', [
            'cartItems' => $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'image' => $item->product->image,
                        'slug' => $item->product->slug,
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ];
            }),
            'total' => $total,
            'itemCount' => $cartItems->sum('quantity')
        ]);
    }

    public function add(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/customer/login')->with('error', 'Debes iniciar sesión para agregar productos al carrito');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:99'
        ]);

        $userId = Auth::id();

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        // Determinar el precio a usar (precio de descuento si existe, sino precio regular)
        $price = $product->discount_price && $product->discount_price < $product->price
                 ? $product->discount_price
                 : $product->price;

        // Verificar si el producto ya está en el carrito
        $existingItem = CartItem::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($existingItem) {
            // Si ya existe, actualizar la cantidad
            $existingItem->quantity += $quantity;
            $existingItem->save();
        } else {
            // Si no existe, crear nuevo item
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price
            ]);
        }

        return back()->with('success', 'Producto agregado al carrito');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        if (!Auth::check()) {
            return redirect('/customer/login')->with('error', 'No autorizado');
        }

        // Verificar que el item pertenece al usuario
        if ($cartItem->user_id !== Auth::id()) {
            return back()->with('error', 'No autorizado');
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return back()->with('success', 'Cantidad actualizada');
    }

    public function remove(CartItem $cartItem)
    {
        if (!Auth::check()) {
            return redirect('/customer/login')->with('error', 'No autorizado');
        }

        // Verificar que el item pertenece al usuario
        if ($cartItem->user_id !== Auth::id()) {
            return back()->with('error', 'No autorizado');
        }

        $cartItem->delete();

        return back()->with('success', 'Producto eliminado del carrito');
    }

    public function clear()
    {
        if (!Auth::check()) {
            return redirect('/customer/login')->with('error', 'No autorizado');
        }

        CartItem::forUser(Auth::id())->delete();

        return back()->with('success', 'Carrito vaciado');
    }

    public function getCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = CartItem::forUser(Auth::id())->sum('quantity');

        return response()->json([
            'count' => $count
        ]);
    }

    public function updateCartPrices()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $userId = Auth::id();
        $cartItems = CartItem::with('product')->forUser($userId)->get();
        $updated = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;
            if ($product) {
                // Determinar el precio correcto
                $correctPrice = $product->discount_price && $product->discount_price < $product->price
                               ? $product->discount_price
                               : $product->price;

                // Solo actualizar si el precio es diferente
                if ($item->price != $correctPrice) {
                    $item->update(['price' => $correctPrice]);
                    $updated++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Se actualizaron {$updated} productos en el carrito",
            'updated_count' => $updated
        ]);
    }
}