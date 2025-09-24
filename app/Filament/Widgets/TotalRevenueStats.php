<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class TotalRevenueStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getHeading(): string
    {
        return '💰 Ingresos y Ventas';
    }

    protected function getStats(): array
    {
        // Ingresos totales
        $totalRevenue = Order::sum('total');

        // Ingresos del mes actual
        $monthlyRevenue = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');

        // Ingresos del mes anterior para comparación
        $lastMonthRevenue = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total');

        // Calcular porcentaje de cambio
        $monthlyChange = $lastMonthRevenue > 0
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Ingresos de hoy
        $todayRevenue = Order::whereDate('created_at', Carbon::today())->sum('total');

        // Ticket promedio
        $totalOrders = Order::count();
        $averageTicket = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            Stat::make('Ingresos Totales', '$' . number_format($totalRevenue, 2))
                ->description('Todos los tiempos')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Ingresos del Mes', '$' . number_format($monthlyRevenue, 2))
                ->description(($monthlyChange >= 0 ? '+' : '') . number_format($monthlyChange, 1) . '% vs mes anterior')
                ->descriptionIcon($monthlyChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyChange >= 0 ? 'success' : 'danger'),

            Stat::make('Ingresos de Hoy', '$' . number_format($todayRevenue, 2))
                ->description('Ventas del día actual')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Ticket Promedio', '$' . number_format($averageTicket, 2))
                ->description('Valor promedio por orden')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),
        ];
    }
}