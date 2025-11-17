@php
    $order = $record;
@endphp

<div class="space-y-6">
    {{-- Información General --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información de la Orden</h3>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Número de Orden</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Fecha</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @endif">
                    @switch($order->status)
                        @case('pending') Pendiente @break
                        @case('completed') Completada @break
                        @case('cancelled') Cancelada @break
                        @case('processing') Procesando @break
                        @default {{ ucfirst($order->status) }}
                    @endswitch
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Estado de Pago</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($order->payment_status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($order->payment_status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                    @endif">
                    @switch($order->payment_status)
                        @case('pending') Pendiente @break
                        @case('approved') Aprobado @break
                        @case('rejected') Rechazado @break
                        @case('cancelled') Cancelado @break
                        @default {{ ucfirst($order->payment_status) }}
                    @endswitch
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Método de Pago</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    @switch($order->payment_method)
                        @case('mercadopago') MercadoPago @break
                        @case('cash') Efectivo @break
                        @case('credit_card') Tarjeta de Crédito @break
                        @case('debit_card') Tarjeta de Débito @break
                        @default {{ ucfirst($order->payment_method) }}
                    @endswitch
                </p>
            </div>
        </div>
    </div>

    {{-- Productos --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Productos</h3>

        <div class="space-y-4">
            @foreach($order->orderDetails as $detail)
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                    <div class="flex items-center space-x-4 flex-1">
                        @if($detail->product && $detail->product->image)
                            <img src="{{ Storage::url($detail->product->image) }}"
                                 alt="{{ $detail->product->name }}"
                                 class="w-16 h-16 object-cover rounded-lg">
                        @else
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif

                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $detail->product ? $detail->product->name : 'Producto no disponible' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Cantidad: {{ $detail->quantity }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Precio unitario: ${{ number_format($detail->price, 2) }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($detail->total, 2) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Resumen de Precios --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen</h3>

        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($order->subtotal, 2) }}</span>
            </div>

            @if($order->discount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Descuento</span>
                    <span class="font-medium text-green-600 dark:text-green-400">-${{ number_format($order->discount, 2) }}</span>
                </div>
            @endif

            @if($order->tax > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Impuestos</span>
                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($order->tax, 2) }}</span>
                </div>
            @endif

            <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                <div class="flex justify-between">
                    <span class="text-base font-semibold text-gray-900 dark:text-white">Total</span>
                    <span class="text-base font-bold text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Información de Envío --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información de Envío</h3>

        <div class="space-y-2 text-sm">
            <p class="text-gray-900 dark:text-white">
                <span class="font-medium">Dirección:</span> {{ $order->shipping_address }}
            </p>
            <p class="text-gray-900 dark:text-white">
                <span class="font-medium">Ciudad:</span> {{ $order->shipping_city }}
            </p>
            <p class="text-gray-900 dark:text-white">
                <span class="font-medium">Provincia/Estado:</span> {{ $order->shipping_state }}
            </p>
            <p class="text-gray-900 dark:text-white">
                <span class="font-medium">País:</span> {{ $order->shipping_country }}
            </p>
            <p class="text-gray-900 dark:text-white">
                <span class="font-medium">Código Postal:</span> {{ $order->shipping_zipcode }}
            </p>
            <p class="text-gray-900 dark:text-white">
                <span class="font-medium">Teléfono:</span> {{ $order->shipping_phone }}
            </p>
        </div>
    </div>

    @if($order->notes)
        {{-- Notas --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notas</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->notes }}</p>
        </div>
    @endif
</div>
