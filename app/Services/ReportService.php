<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Genera el reporte completo con todos los datos filtrados por fecha
     */
    public static function generateReport(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate) : null;
        $end = $endDate ? Carbon::parse($endDate) : null;

        return [
            'period' => self::formatPeriod($start, $end),
            'start_date' => $start?->format('d/m/Y'),
            'end_date' => $end?->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y H:i'),
            'revenue_stats' => self::getRevenueStats($start, $end),
            'order_stats' => self::getOrderStats($start, $end),
            'customer_stats' => self::getCustomerStats($start, $end),
            'top_products' => self::getTopProducts($start, $end),
            'product_revenue' => self::getProductRevenue($start, $end),
            'order_status_breakdown' => self::getOrderStatusBreakdown($start, $end),
            'category_sales' => self::getCategorySales($start, $end),
        ];
    }

    /**
     * Formatea el período de fechas para mostrar
     */
    private static function formatPeriod(?Carbon $start, ?Carbon $end): string
    {
        if (!$start && !$end) {
            return 'Todos los tiempos';
        }

        if ($start && $end) {
            return $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
        }

        if ($start) {
            return 'Desde ' . $start->format('d/m/Y');
        }

        return 'Hasta ' . $end->format('d/m/Y');
    }

    /**
     * Obtiene estadísticas de ingresos
     */
    public static function getRevenueStats(?Carbon $start, ?Carbon $end): array
    {
        $query = Order::query()
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end));

        $totalRevenue = (clone $query)->sum('total');
        $totalOrders = (clone $query)->count();
        $averageTicket = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Calcular subtotal y descuentos
        $subtotal = (clone $query)->sum('subtotal');
        $discount = (clone $query)->sum('discount');
        $tax = (clone $query)->sum('tax');

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_ticket' => $averageTicket,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
        ];
    }

    /**
     * Obtiene estadísticas de órdenes
     */
    public static function getOrderStats(?Carbon $start, ?Carbon $end): array
    {
        $query = Order::query()
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end));

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'preparing' => (clone $query)->where('status', 'preparing')->count(),
            'shipped' => (clone $query)->where('status', 'shipped')->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Obtiene estadísticas de clientes
     */
    public static function getCustomerStats(?Carbon $start, ?Carbon $end): array
    {
        $query = Order::query()
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end));

        $uniqueCustomers = (clone $query)->distinct('user_id')->count('user_id');
        $totalCustomers = User::count();
        $newCustomers = User::query()
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->count();

        return [
            'unique_customers' => $uniqueCustomers,
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
        ];
    }

    /**
     * Obtiene los productos más vendidos
     */
    public static function getTopProducts(?Carbon $start, ?Carbon $end, int $limit = 10): array
    {
        $query = OrderDetail::query()
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->when($start, fn($q) => $q->whereDate('orders.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('orders.created_at', '<=', $end))
            ->selectRaw('
                products.name,
                SUM(order_details.quantity) as total_quantity,
                SUM(order_details.quantity * order_details.price) as total_revenue,
                COUNT(DISTINCT orders.id) as order_count
            ')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();

        return $query->map(function ($item) {
            return [
                'name' => $item->name,
                'quantity' => (int) $item->total_quantity,
                'revenue' => (float) $item->total_revenue,
                'orders' => (int) $item->order_count,
            ];
        })->toArray();
    }

    /**
     * Obtiene recaudación por producto
     */
    public static function getProductRevenue(?Carbon $start, ?Carbon $end, int $limit = 15): array
    {
        $query = OrderDetail::query()
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->when($start, fn($q) => $q->whereDate('orders.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('orders.created_at', '<=', $end))
            ->selectRaw('
                products.name as product_name,
                categories.name as category_name,
                SUM(order_details.quantity) as total_quantity,
                SUM(order_details.quantity * order_details.price) as total_revenue,
                AVG(order_details.price) as avg_price
            ')
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return $query->map(function ($item) {
            return [
                'name' => $item->product_name,
                'category' => $item->category_name ?? 'Sin categoría',
                'quantity' => (int) $item->total_quantity,
                'avg_price' => (float) $item->avg_price,
                'revenue' => (float) $item->total_revenue,
            ];
        })->toArray();
    }

    /**
     * Obtiene desglose de estado de órdenes
     */
    public static function getOrderStatusBreakdown(?Carbon $start, ?Carbon $end): array
    {
        $query = Order::query()
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->selectRaw('status, COUNT(*) as count, SUM(total) as revenue')
            ->groupBy('status')
            ->get();

        $totalOrders = $query->sum('count');

        $statusLabels = [
            'pending' => 'Pendientes',
            'preparing' => 'En Preparación',
            'shipped' => 'En Camino',
            'delivered' => 'Entregados',
            'cancelled' => 'Cancelados',
        ];

        return $query->map(function ($item) use ($statusLabels, $totalOrders) {
            return [
                'status' => $item->status,
                'label' => $statusLabels[$item->status] ?? ucfirst($item->status),
                'count' => (int) $item->count,
                'revenue' => (float) $item->revenue,
                'percentage' => $totalOrders > 0 ? ($item->count / $totalOrders) * 100 : 0,
            ];
        })->toArray();
    }

    /**
     * Obtiene ventas por categoría
     */
    public static function getCategorySales(?Carbon $start, ?Carbon $end): array
    {
        $query = OrderDetail::query()
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->when($start, fn($q) => $q->whereDate('orders.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('orders.created_at', '<=', $end))
            ->selectRaw('
                COALESCE(categories.name, "Sin categoría") as category_name,
                SUM(order_details.quantity) as total_quantity,
                SUM(order_details.quantity * order_details.price) as total_revenue
            ')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        $totalRevenue = $query->sum('total_revenue');

        return $query->map(function ($item) use ($totalRevenue) {
            return [
                'category' => $item->category_name,
                'quantity' => (int) $item->total_quantity,
                'revenue' => (float) $item->total_revenue,
                'percentage' => $totalRevenue > 0 ? ($item->total_revenue / $totalRevenue) * 100 : 0,
            ];
        })->toArray();
    }
}
