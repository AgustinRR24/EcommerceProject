<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\OrderDetail;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductsStats extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getHeading(): string
    {
        return '📦 Inventario y Productos';
    }

    protected function getStats(): array
    {
        // Total de productos
        $totalProducts = Product::count();

        // Productos activos
        $activeProducts = Product::where('is_active', true)->count();

        // Productos con stock bajo (menos de 10)
        $lowStockProducts = Product::where('stock', '<', 10)
            ->where('is_active', true)
            ->count();

        // Productos sin stock
        $outOfStockProducts = Product::where('stock', 0)
            ->where('is_active', true)
            ->count();

        // Total de ventas de productos
        $totalProductsSold = OrderDetail::sum('quantity');

        return [
            Stat::make('Total Productos', number_format($totalProducts))
                ->description('Productos en catálogo')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Productos Activos', number_format($activeProducts))
                ->description('Disponibles para venta')
                ->descriptionIcon('heroicon-m-check')
                ->color('success'),

            Stat::make('Stock Bajo', number_format($lowStockProducts))
                ->description('Menos de 10 unidades')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Sin Stock', number_format($outOfStockProducts))
                ->description('Requieren reposición')
                ->descriptionIcon('heroicon-m-x-mark')
                ->color('danger'),
        ];
    }
}