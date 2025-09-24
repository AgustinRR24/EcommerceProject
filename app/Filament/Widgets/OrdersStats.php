<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class OrdersStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getHeading(): string
    {
        return '📦 Órdenes y Pedidos';
    }

    protected function getStats(): array
    {
        // Total de órdenes
        $totalOrders = Order::count();

        // Órdenes de este mes
        $monthlyOrders = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Órdenes pendientes
        $pendingOrders = Order::where('status', 'pending')->count();

        // Órdenes completadas
        $completedOrders = Order::where('status', 'completed')->count();

        // Órdenes de hoy
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();

        // Nuevos clientes este mes
        $newCustomers = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return [
            Stat::make('Total Órdenes', number_format($totalOrders))
                ->description('Todas las órdenes')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Órdenes del Mes', number_format($monthlyOrders))
                ->description('Órdenes de ' . Carbon::now()->format('M Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Órdenes Pendientes', number_format($pendingOrders))
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Órdenes de Hoy', number_format($todayOrders))
                ->description('Ventas del día')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
        ];
    }
}