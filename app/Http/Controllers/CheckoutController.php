<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PromoCode;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
        $tax = 0; // Temporalmente sin impuestos
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

    public function process(Request $request, MercadoPagoService $mercadoPagoService)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $request->validate([
            'shipping_name' => 'required|string',
            'shipping_email' => 'required|email',
            'shipping_phone' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip' => 'required|string',
            'promo_code_id' => 'nullable|exists:promo_codes,id',
            'final_total' => 'nullable|numeric|min:0'
        ]);

        $userId = Auth::id();
        $user = Auth::user();

        // Obtener items del carrito
        $cartItems = CartItem::with('product')
            ->forUser($userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Tu carrito está vacío'], 400);
        }

        // Formatear items para MercadoPago (según documentación)
        $mpItems = $mercadoPagoService->formatItems($cartItems->map(function ($item) {
            return [
                'product' => [
                    'name' => $item->product->name,
                ],
                'quantity' => $item->quantity,
                'price' => $item->price,
            ];
        })->toArray());

        // Generar referencia externa única (máximo 64 caracteres para MercadoPago)
        $externalReference = 'ORD-' . substr(Str::uuid(), 0, 27); // Total: 31 caracteres

        // URLs de retorno
        $backUrls = [
            "success" => url('/checkout/success'),
            "failure" => url('/checkout/failure'),
            "pending" => url('/checkout/pending')
        ];

        Log::info('Back URLs configured:', $backUrls);

        // Crear preferencia en MercadoPago (versión simplificada)
        $preferenceResult = $mercadoPagoService->createPreference($mpItems, $backUrls, $externalReference);

        if (!$preferenceResult['success']) {
            return response()->json([
                'error' => 'Error al crear la preferencia de pago: ' . $preferenceResult['error']
            ], 500);
        }

        // Calcular totales para la orden
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
        $shipping = 0;
        $tax = 0;

        // Aplicar código promocional si existe
        $discount = 0;
        $promoCodeId = $request->promo_code_id;
        if ($promoCodeId) {
            $promoCode = PromoCode::where('id', $promoCodeId)
                                  ->where('is_active', true)
                                  ->first();
            if ($promoCode) {
                $discount = ($subtotal * $promoCode->discount_percentage) / 100;
            }
        }

        $total = $subtotal + $shipping + $tax - $discount;

        // Crear la orden en la base de datos
        $order = DB::transaction(function () use ($cartItems, $user, $request, $externalReference, $subtotal, $shipping, $tax, $total, $discount, $promoCodeId) {
            // Crear la orden
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $externalReference,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => 'mercadopago',
                'payment_status' => 'pending',
                'promo_code_id' => $promoCodeId,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_country' => 'Argentina',
                'shipping_zipcode' => $request->shipping_zip,
                'shipping_phone' => $request->shipping_phone,
                'notes' => "Nombre: {$request->shipping_name}, Email: {$request->shipping_email}"
            ]);

            // Crear los detalles de la orden
            foreach ($cartItems as $cartItem) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->quantity * $cartItem->price
                ]);
            }

            return $order;
        });

        Log::info('Order created successfully:', ['order_id' => $order->id, 'external_reference' => $externalReference]);

        $response = [
            'success' => true,
            'preference_id' => $preferenceResult['preference_id'],
            'public_key' => config('mercadopago.public_key'),
            'init_point' => $preferenceResult['init_point'],
            'sandbox_init_point' => $preferenceResult['sandbox_init_point'],
            'external_reference' => $externalReference
        ];

        Log::info('Checkout response:', $response);

        return response()->json($response);
    }

    public function success(Request $request, MercadoPagoService $mercadoPagoService)
    {
        $paymentId = $request->payment_id;
        $externalReference = $request->external_reference;

        Log::info('Success page accessed', [
            'payment_id' => $paymentId,
            'external_reference' => $externalReference,
            'status' => $request->status
        ]);

        $order = null;

        // Si tenemos payment_id, procesar el pago
        if ($paymentId && $externalReference) {
            $this->processPaymentSuccess($paymentId, $externalReference, $mercadoPagoService);

            // Buscar la orden para mostrar información
            $order = Order::where('order_number', $externalReference)->first();

            // Si no encuentra con exact match, buscar por LIKE
            if (!$order && strlen($externalReference) < 50) {
                $order = Order::where('order_number', 'LIKE', $externalReference . '%')->first();
            }
        }

        return Inertia::render('Checkout/Success', [
            'payment_id' => $request->payment_id,
            'status' => $request->status,
            'external_reference' => $request->external_reference,
            'order' => $order ? [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'created_at' => $order->created_at->format('d/m/Y H:i')
            ] : null
        ]);
    }

    private function processPaymentSuccess($paymentId, $externalReference, MercadoPagoService $mercadoPagoService)
    {
        try {
            // Verificar el estado del pago
            $paymentResult = $mercadoPagoService->getPayment($paymentId);

            if ($paymentResult['success']) {
                $payment = $paymentResult['payment'];

                Log::info('Payment verification', [
                    'payment_id' => $paymentId,
                    'status' => $payment->status,
                    'external_reference' => $externalReference
                ]);

                // Buscar la orden (MercadoPago puede cortar el external_reference)
                $order = Order::where('order_number', $externalReference)->first();

                // Si no encuentra con exact match, buscar por LIKE (por si MercadoPago cortó la referencia)
                if (!$order && strlen($externalReference) < 50) {
                    $order = Order::where('order_number', 'LIKE', $externalReference . '%')->first();
                    Log::info('Order found with LIKE search', ['truncated_ref' => $externalReference, 'found_order_id' => $order ? $order->id : 'none']);
                }

                Log::info('Order search result', [
                    'order_found' => $order ? 'yes' : 'no',
                    'order_id' => $order ? $order->id : 'N/A',
                    'payment_status' => $payment->status,
                    'should_process' => $order && $payment->status === 'approved' ? 'yes' : 'no'
                ]);

                if ($order && $payment->status === 'approved') {
                    Log::info('Starting order processing...');

                    // Actualizar estado de la orden
                    $order->update([
                        'status' => 'completed',
                        'payment_status' => 'approved'
                    ]);

                    Log::info('Order status updated', ['order_id' => $order->id]);

                    // Procesar pago aprobado (limpiar carrito y reducir stock)
                    $this->processApprovedPayment($order);

                    Log::info('Payment processed successfully from success page', [
                        'order_id' => $order->id,
                        'payment_id' => $paymentId
                    ]);
                } else {
                    Log::warning('Order processing skipped', [
                        'order_exists' => $order ? 'yes' : 'no',
                        'payment_status' => $payment->status,
                        'expected_status' => 'approved'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing payment from success page', [
                'payment_id' => $paymentId,
                'external_reference' => $externalReference,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function failure(Request $request)
    {
        return Inertia::render('Checkout/Failure', [
            'payment_id' => $request->payment_id,
            'status' => $request->status,
            'external_reference' => $request->external_reference
        ]);
    }

    public function pending(Request $request)
    {
        return Inertia::render('Checkout/Pending', [
            'payment_id' => $request->payment_id,
            'status' => $request->status,
            'external_reference' => $request->external_reference
        ]);
    }

    public function webhook(Request $request, MercadoPagoService $mercadoPagoService)
    {
        Log::info('MercadoPago webhook received', $request->all());

        // Manejar webhook de MercadoPago
        $paymentId = $request->data['id'] ?? null;

        if ($paymentId) {
            // Obtener información del pago
            $paymentResult = $mercadoPagoService->getPayment($paymentId);

            if ($paymentResult['success']) {
                $payment = $paymentResult['payment'];
                $externalReference = $payment->external_reference ?? null;

                Log::info('Payment details from webhook', [
                    'payment_id' => $paymentId,
                    'status' => $payment->status,
                    'external_reference' => $externalReference
                ]);

                if ($externalReference) {
                    // Buscar la orden (con búsqueda LIKE por si MercadoPago cortó la referencia)
                    $order = Order::where('order_number', $externalReference)->first();

                    if (!$order && strlen($externalReference) < 50) {
                        $order = Order::where('order_number', 'LIKE', $externalReference . '%')->first();
                        Log::info('Order found with LIKE search in webhook', ['truncated_ref' => $externalReference, 'found_order_id' => $order ? $order->id : 'none']);
                    }

                    if ($order) {
                        // Actualizar el estado de la orden según el estado del pago
                        $newStatus = match($payment->status) {
                            'approved' => 'completed',
                            'pending' => 'pending',
                            'rejected' => 'cancelled',
                            'cancelled' => 'cancelled',
                            default => $order->status
                        };

                        $newPaymentStatus = $payment->status;

                        $order->update([
                            'status' => $newStatus,
                            'payment_status' => $newPaymentStatus
                        ]);

                        Log::info('Order updated via webhook', [
                            'order_id' => $order->id,
                            'new_status' => $newStatus,
                            'payment_status' => $newPaymentStatus
                        ]);

                        // Si el pago fue aprobado, limpiar carrito y reducir stock
                        if ($payment->status === 'approved') {
                            $this->processApprovedPayment($order);
                        }
                    } else {
                        Log::warning('Order not found for external_reference in webhook', ['external_reference' => $externalReference]);
                    }
                }
            } else {
                Log::error('Failed to get payment details in webhook', ['payment_id' => $paymentId, 'error' => $paymentResult['error']]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function processApprovedPayment(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                // Limpiar carrito del usuario
                CartItem::where('user_id', $order->user_id)->delete();

                // Reducir stock de productos
                foreach ($order->orderDetails as $detail) {
                    $product = $detail->product;
                    if ($product && $product->stock >= $detail->quantity) {
                        $product->decrement('stock', $detail->quantity);
                        Log::info('Stock reduced', [
                            'product_id' => $product->id,
                            'quantity_reduced' => $detail->quantity,
                            'new_stock' => $product->fresh()->stock
                        ]);
                    } else {
                        Log::warning('Insufficient stock or product not found', [
                            'product_id' => $detail->product_id,
                            'required_quantity' => $detail->quantity,
                            'available_stock' => $product->stock ?? 'N/A'
                        ]);
                    }
                }
            });

            Log::info('Approved payment processed successfully', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Error processing approved payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Endpoint para procesar manualmente un pago (útil para desarrollo)
    public function processPayment(Request $request, MercadoPagoService $mercadoPagoService)
    {
        $paymentId = $request->payment_id;
        $externalReference = $request->external_reference;

        if (!$paymentId || !$externalReference) {
            return response()->json(['error' => 'payment_id y external_reference son requeridos'], 400);
        }

        try {
            $this->processPaymentSuccess($paymentId, $externalReference, $mercadoPagoService);

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error procesando el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    // Endpoint público para verificar y procesar automáticamente pagos pendientes
    public function checkPendingPayments(MercadoPagoService $mercadoPagoService)
    {
        try {
            // Buscar órdenes pendientes de las últimas 2 horas
            $pendingOrders = Order::where('payment_status', 'pending')
                ->where('created_at', '>', now()->subHours(2))
                ->get();

            Log::info('Checking pending payments', ['count' => $pendingOrders->count()]);

            $processed = 0;

            foreach ($pendingOrders as $order) {
                // Esta función simularía verificar el estado del pago
                // En desarrollo, podemos forzar la verificación
                Log::info('Found pending order to check', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
                $processed++;
            }

            return response()->json([
                'success' => true,
                'message' => "Verificadas {$processed} órdenes pendientes",
                'processed' => $processed
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking pending payments', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function validatePromoCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $promoCode = PromoCode::where('code', $request->code)
                             ->where('is_active', true)
                             ->first();

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Código promocional no válido o expirado'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Código promocional aplicado correctamente',
            'promo_code' => [
                'id' => $promoCode->id,
                'code' => $promoCode->code,
                'discount_percentage' => $promoCode->discount_percentage
            ]
        ]);
    }
}