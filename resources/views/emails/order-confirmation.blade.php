<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 30px 20px;
        }
        .success-icon {
            text-align: center;
            font-size: 64px;
            margin-bottom: 20px;
        }
        .order-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .order-info h2 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: #667eea;
        }
        .order-info p {
            margin: 5px 0;
        }
        .order-details {
            margin: 20px 0;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-details th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .order-details td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .shipping-info {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .shipping-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #856404;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .discount-badge {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Tech Store Premium</h1>
        </div>

        <div class="email-body">
            <div class="success-icon">✅</div>

            <h2 style="text-align: center; color: #28a745; margin-bottom: 10px;">¡Pago Confirmado!</h2>
            <p style="text-align: center; font-size: 16px; color: #6c757d;">
                Gracias por tu compra. Tu pedido ha sido confirmado y está siendo procesado.
            </p>

            <div class="order-info">
                <h2>Información del Pedido</h2>
                <p><strong>Número de Pedido:</strong> {{ $order->order_number }}</p>
                <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong>
                    @if($order->payment_status === 'approved')
                        <span style="color: #28a745;">✓ Pago Aprobado</span>
                    @else
                        <span style="color: #ffc107;">Pendiente</span>
                    @endif
                </p>
                <p><strong>Método de Pago:</strong> MercadoPago</p>
            </div>

            <div class="order-details">
                <h3>Detalles del Pedido</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderDetails as $detail)
                        <tr>
                            <td>{{ $detail->product->name }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>${{ number_format($detail->price, 2) }}</td>
                            <td>${{ number_format($detail->total, 2) }}</td>
                        </tr>
                        @endforeach

                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                            <td><strong>${{ number_format($order->subtotal, 2) }}</strong></td>
                        </tr>

                        @if($order->discount > 0)
                        <tr>
                            <td colspan="3" style="text-align: right;">
                                <strong>Descuento:</strong>
                                @if($order->promoCode)
                                    <span class="discount-badge">{{ $order->promoCode->code }}</span>
                                @endif
                            </td>
                            <td><strong style="color: #dc3545;">-${{ number_format($order->discount, 2) }}</strong></td>
                        </tr>
                        @endif

                        @if($order->tax > 0)
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Impuestos:</strong></td>
                            <td><strong>${{ number_format($order->tax, 2) }}</strong></td>
                        </tr>
                        @endif

                        <tr class="total-row">
                            <td colspan="3" style="text-align: right; font-size: 18px;">
                                <strong>TOTAL:</strong>
                            </td>
                            <td style="font-size: 18px; color: #28a745;">
                                <strong>${{ number_format($order->total, 2) }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="shipping-info">
                <h3>📦 Información de Envío</h3>
                <p><strong>Dirección:</strong> {{ $order->shipping_address }}</p>
                <p><strong>Ciudad:</strong> {{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
                <p><strong>Código Postal:</strong> {{ $order->shipping_zipcode }}</p>
                <p><strong>País:</strong> {{ $order->shipping_country }}</p>
                <p><strong>Teléfono:</strong> {{ $order->shipping_phone }}</p>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #6c757d;">
                    Recibirás un email adicional cuando tu pedido sea enviado con el número de seguimiento.
                </p>
                <a href="{{ url('/') }}" class="button">Volver a la Tienda</a>
            </div>
        </div>

        <div class="footer">
            <p>
                <strong>Tech Store Premium</strong><br>
                La mejor tecnología al alcance de tus manos<br>
                <br>
                ¿Necesitas ayuda? Contáctanos en <a href="mailto:soporte@techstore.com">soporte@techstore.com</a>
            </p>
            <p style="font-size: 12px; color: #adb5bd; margin-top: 15px;">
                Este es un email automático, por favor no respondas a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>
