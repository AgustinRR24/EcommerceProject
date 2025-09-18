<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index()
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect('/customer/login')->with('message', 'Debes iniciar sesión para proceder al checkout');
        }

        $userId = Auth::id();

        // Obtener items del carrito
        $cartItems = CartItem::with('product')
            ->forUser($userId)
            ->get();

        // Verificar que hay items en el carrito
        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('error', 'Tu carrito está vacío');
        }

        // Calcular totales
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $shipping = 0; // Envío gratis
        $tax = $subtotal * 0.1; // 10% de impuestos
        $total = $subtotal + $shipping + $tax;

        return Inertia::render('Checkout', [
            'cartItems' => $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'image' => $item->product->image,
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ];
            }),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'user' => Auth::user()
        ]);
    }

    public function process(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        // Aquí iría el procesamiento del pago
        // Por ahora solo devolvemos un mensaje de éxito

        return response()->json([
            'success' => true,
            'message' => 'Pedido procesado exitosamente'
        ]);
    }
}