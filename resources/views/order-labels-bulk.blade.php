<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Etiquetas de Envío - Impresión Masiva</title>
    <style>
        @page {
            size: A4;
            margin: 5mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }

        .label-container {
            width: 9cm;
            height: 14cm;
            padding: 5mm;
            display: inline-block;
            vertical-align: top;
            margin: 2mm;
            border: 1px dashed #ccc;
            page-break-inside: avoid;
        }

        .label-content {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sender-section {
            border-bottom: 2px solid #000;
            padding-bottom: 3mm;
            margin-bottom: 4mm;
        }

        .sender-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2mm;
            color: #333;
        }

        .sender-info {
            font-size: 11px;
            line-height: 1.3;
        }

        .recipient-section {
            flex-grow: 1;
            margin-bottom: 4mm;
        }

        .recipient-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2mm;
            color: #333;
        }

        .recipient-info {
            font-size: 12px;
            line-height: 1.3;
        }

        .package-section {
            border: 2px solid #000;
            padding: 3mm;
            margin: 3mm 0;
            background-color: #f9f9f9;
        }

        .package-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 2mm;
            color: #333;
        }

        .package-info {
            font-size: 11px;
            line-height: 1.3;
        }

        .footer-info {
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 3mm;
        }

        .order-number {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 2mm;
            color: #000;
        }

        .shipping-date {
            font-size: 11px;
            color: #666;
        }

        .text-truncate {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .notes {
            font-size: 10px;
            font-style: italic;
            margin-top: 2mm;
            color: #666;
            background-color: #fffacd;
            padding: 2mm;
            border-left: 3px solid #ffd700;
        }

        /* Asegurar que cada par de etiquetas esté en una nueva página */
        .page-break {
            page-break-after: always;
        }

        @media print {
            .label-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    @foreach($records as $index => $record)
    <div class="label-container">
        <div class="label-content">
            <!-- Información del emisor/remitente -->
            <div class="sender-section">
                <div class="sender-title">EMISOR:</div>
                <div class="sender-info">
                    <div class="text-truncate">{{ env('SHIPPING_SENDER_NAME', 'Tu Empresa') }}</div>
                    <div class="text-truncate">{{ env('SHIPPING_SENDER_ADDRESS', '') }}</div>
                    <div class="text-truncate">{{ env('SHIPPING_SENDER_CITY', '') }}, {{ env('SHIPPING_SENDER_ZIPCODE', '') }}</div>
                </div>
            </div>

            <!-- Información del destinatario -->
            <div class="recipient-section">
                <div class="recipient-title">DESTINATARIO:</div>
                <div class="recipient-info">
                    <div class="text-truncate"><strong>{{ $record->user->name }}</strong></div>
                    <div class="text-truncate">{{ $record->shipping_address }}</div>
                    <div class="text-truncate">{{ $record->shipping_city }}, {{ $record->shipping_state }}</div>
                    <div class="text-truncate">{{ $record->shipping_zipcode }} - {{ $record->shipping_country }}</div>
                    @if($record->shipping_phone)
                    <div class="text-truncate">Tel: {{ $record->shipping_phone }}</div>
                    @endif
                </div>
            </div>

            <!-- Detalles del paquete -->
            <div class="package-section">
                <div class="package-title">DETALLES PAQUETE:</div>
                <div class="package-info">
                    @foreach($record->orderDetails as $detail)
                    <div class="text-truncate">{{ $detail->quantity }}x {{ $detail->product->name ?? 'Producto' }}</div>
                    @endforeach
                    <div class="text-truncate"><strong>Valor: ${{ number_format($record->total, 2) }}</strong></div>
                </div>
            </div>

            <!-- Información del pie -->
            <div class="footer-info">
                <div class="order-number">{{ $record->order_number }}</div>
                <div class="shipping-date">Fecha envío: {{ now()->format('d/m/Y') }}</div>
                @if($record->notes)
                <div class="notes text-truncate">{{ $record->notes }}</div>
                @endif
            </div>
        </div>
    </div>

    @if(($index + 1) % 2 == 0 && !$loop->last)
    <div class="page-break"></div>
    @endif
    @endforeach
</body>
</html>
