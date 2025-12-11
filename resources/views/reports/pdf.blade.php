<div style="font-family: Arial, sans-serif; padding: 25px; max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="background: #2c3e50; color: white; padding: 25px; text-align: center; margin-bottom: 30px;">
        <h1 style="margin: 0; font-size: 24px; font-weight: normal; letter-spacing: 1px;">REPORTE DE VENTAS</h1>
        <p style="margin: 8px 0 0 0; font-size: 13px; opacity: 0.9;">{{ $data['period'] }}</p>
    </div>

    <!-- Resumen Ejecutivo -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #2c3e50; font-size: 14px; text-transform: uppercase; border-bottom: 2px solid #ecf0f1; padding-bottom: 8px; margin-bottom: 15px; letter-spacing: 0.5px;">Resumen Ejecutivo</h3>
        <table style="width: 100%; margin-bottom: 15px;">
            <tr>
                <td style="width: 33.33%; background: #f8f9fa; padding: 15px; border-left: 3px solid #2c3e50; vertical-align: top;">
                    <p style="margin: 0; font-size: 9px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px;">Ingresos Totales</p>
                    <p style="margin: 5px 0 0 0; font-size: 20px; font-weight: bold; color: #2c3e50;">${{ number_format($data['revenue_stats']['total_revenue'], 2) }}</p>
                </td>
                <td style="width: 33.33%; background: #f8f9fa; padding: 15px; border-left: 3px solid #2c3e50; vertical-align: top;">
                    <p style="margin: 0; font-size: 9px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px;">Total Órdenes</p>
                    <p style="margin: 5px 0 0 0; font-size: 20px; font-weight: bold; color: #2c3e50;">{{ number_format($data['order_stats']['total']) }}</p>
                </td>
                <td style="width: 33.33%; background: #f8f9fa; padding: 15px; border-left: 3px solid #2c3e50; vertical-align: top;">
                    <p style="margin: 0; font-size: 9px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px;">Ticket Promedio</p>
                    <p style="margin: 5px 0 0 0; font-size: 20px; font-weight: bold; color: #2c3e50;">${{ number_format($data['revenue_stats']['average_ticket'], 2) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Estado de Órdenes -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #2c3e50; font-size: 14px; text-transform: uppercase; border-bottom: 2px solid #ecf0f1; padding-bottom: 8px; margin-bottom: 15px; letter-spacing: 0.5px;">Estado de Órdenes</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <thead>
                <tr style="background: #34495e; color: white;">
                    <th style="padding: 8px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 9px;">Estado</th>
                    <th style="padding: 8px; text-align: center; font-weight: 600; text-transform: uppercase; font-size: 9px;">Cantidad</th>
                    <th style="padding: 8px; text-align: center; font-weight: 600; text-transform: uppercase; font-size: 9px;">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['order_status_breakdown'] as $status)
                <tr style="border-bottom: 1px solid #ecf0f1;">
                    <td style="padding: 7px 8px;">{{ $status['label'] }}</td>
                    <td style="padding: 7px 8px; text-align: center; font-weight: bold;">{{ number_format($status['count']) }}</td>
                    <td style="padding: 7px 8px; text-align: center;">
                        <span style="background: #ecf0f1; padding: 2px 6px; font-weight: 600; font-size: 9px;">{{ number_format($status['percentage'], 1) }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top 10 Productos -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #2c3e50; font-size: 14px; text-transform: uppercase; border-bottom: 2px solid #ecf0f1; padding-bottom: 8px; margin-bottom: 15px; letter-spacing: 0.5px;">Top 10 Productos Más Vendidos</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <thead>
                <tr style="background: #34495e; color: white;">
                    <th style="padding: 8px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 9px;">Producto</th>
                    <th style="padding: 8px; text-align: center; font-weight: 600; text-transform: uppercase; font-size: 9px;">Unidades</th>
                    <th style="padding: 8px; text-align: right; font-weight: 600; text-transform: uppercase; font-size: 9px;">Recaudación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['top_products'] as $index => $product)
                <tr style="border-bottom: 1px solid #ecf0f1; {{ $index < 3 ? 'background: #ecf0f1;' : '' }}">
                    <td style="padding: 7px 8px; {{ $index < 3 ? 'font-weight: bold;' : '' }}">{{ $product['name'] }}</td>
                    <td style="padding: 7px 8px; text-align: center; font-weight: bold;">{{ number_format($product['quantity']) }}</td>
                    <td style="padding: 7px 8px; text-align: right; color: #27ae60; font-weight: bold;">${{ number_format($product['revenue'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Ventas por Categoría -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #2c3e50; font-size: 14px; text-transform: uppercase; border-bottom: 2px solid #ecf0f1; padding-bottom: 8px; margin-bottom: 15px; letter-spacing: 0.5px;">Ventas por Categoría</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <thead>
                <tr style="background: #34495e; color: white;">
                    <th style="padding: 8px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 9px;">Categoría</th>
                    <th style="padding: 8px; text-align: center; font-weight: 600; text-transform: uppercase; font-size: 9px;">Productos</th>
                    <th style="padding: 8px; text-align: right; font-weight: 600; text-transform: uppercase; font-size: 9px;">Total</th>
                    <th style="padding: 8px; text-align: center; font-weight: 600; text-transform: uppercase; font-size: 9px;">% del Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['category_sales'] as $category)
                <tr style="border-bottom: 1px solid #ecf0f1;">
                    <td style="padding: 7px 8px; font-weight: bold;">{{ $category['category'] }}</td>
                    <td style="padding: 7px 8px; text-align: center;">{{ number_format($category['quantity']) }}</td>
                    <td style="padding: 7px 8px; text-align: right; color: #27ae60; font-weight: bold;">${{ number_format($category['revenue'], 2) }}</td>
                    <td style="padding: 7px 8px; text-align: center;">
                        <span style="background: #ecf0f1; padding: 2px 6px; font-weight: 600; font-size: 9px;">{{ number_format($category['percentage'], 1) }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Estadísticas de Clientes -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #2c3e50; font-size: 14px; text-transform: uppercase; border-bottom: 2px solid #ecf0f1; padding-bottom: 8px; margin-bottom: 15px; letter-spacing: 0.5px;">Estadísticas de Clientes</h3>
        <table style="width: 100%; margin-bottom: 15px;">
            <tr>
                <td style="width: 50%; background: #f8f9fa; padding: 15px; border-left: 3px solid #2c3e50; vertical-align: top;">
                    <p style="margin: 0; font-size: 9px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px;">Total Clientes</p>
                    <p style="margin: 5px 0 0 0; font-size: 20px; font-weight: bold; color: #2c3e50;">{{ number_format($data['customer_stats']['total_customers']) }}</p>
                </td>
                <td style="width: 50%; background: #f8f9fa; padding: 15px; border-left: 3px solid #2c3e50; vertical-align: top;">
                    <p style="margin: 0; font-size: 9px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px;">Nuevos Clientes</p>
                    <p style="margin: 5px 0 0 0; font-size: 20px; font-weight: bold; color: #2c3e50;">{{ number_format($data['customer_stats']['new_customers']) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Análisis Detallado de Productos -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #2c3e50; font-size: 14px; text-transform: uppercase; border-bottom: 2px solid #ecf0f1; padding-bottom: 8px; margin-bottom: 15px; letter-spacing: 0.5px;">Análisis Detallado de Productos</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <thead>
                <tr style="background: #34495e; color: white;">
                    <th style="padding: 8px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 9px;">Producto</th>
                    <th style="padding: 8px; text-align: center; font-weight: 600; text-transform: uppercase; font-size: 9px;">Cantidad</th>
                    <th style="padding: 8px; text-align: right; font-weight: 600; text-transform: uppercase; font-size: 9px;">Precio Prom.</th>
                    <th style="padding: 8px; text-align: right; font-weight: 600; text-transform: uppercase; font-size: 9px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['product_revenue'] as $product)
                <tr style="border-bottom: 1px solid #ecf0f1;">
                    <td style="padding: 7px 8px;">{{ $product['name'] }}</td>
                    <td style="padding: 7px 8px; text-align: center;">{{ number_format($product['quantity']) }}</td>
                    <td style="padding: 7px 8px; text-align: right;">${{ number_format($product['avg_price'], 2) }}</td>
                    <td style="padding: 7px 8px; text-align: right; color: #27ae60; font-weight: bold;">${{ number_format($product['revenue'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div style="margin-top: 40px; padding-top: 15px; border-top: 1px solid #ecf0f1; text-align: center;">
        <p style="margin: 0; color: #7f8c8d; font-size: 9px;">Generado el {{ $data['generated_at'] }}</p>
        <p style="margin: 3px 0 0 0; color: #95a5a6; font-size: 8px;">Este reporte fue generado automáticamente por el sistema</p>
    </div>
</div>
