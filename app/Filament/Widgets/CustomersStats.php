<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class CustomersStats extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getHeading(): string
    {
        return '👥 Clientes y Usuarios';
    }

    protected function getStats(): array
    {
        // Total de clientes
        $totalCustomers = User::count();

        // Nuevos clientes este mes
        $newCustomersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Nuevos clientes mes anterior
        $newCustomersLastMonth = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        // Calcular crecimiento
        $customerGrowth = $newCustomersLastMonth > 0
            ? (($newCustomersThisMonth - $newCustomersLastMonth) / $newCustomersLastMonth) * 100
            : 0;

        // Clientes con órdenes (clientes activos)
        $activeCustomers = User::whereHas('orders')->count();

        // Nuevos clientes hoy
        $newCustomersToday = User::whereDate('created_at', Carbon::today())->count();

        return [
            Stat::make('Total Clientes', number_format($totalCustomers))
                ->description('Clientes registrados')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Nuevos este Mes', number_format($newCustomersThisMonth))
                ->description(($customerGrowth >= 0 ? '+' : '') . number_format($customerGrowth, 1) . '% vs mes anterior')
                ->descriptionIcon($customerGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($customerGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Clientes Activos', number_format($activeCustomers))
                ->description('Con al menos 1 orden')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),

            Stat::make('Nuevos Hoy', number_format($newCustomersToday))
                ->description('Registros del día')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}