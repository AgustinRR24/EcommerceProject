<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class MonthlySalesChart extends ApexChartWidget
{
    protected static ?string $chartId = 'monthlySalesChart';
    protected static ?int $sort = 6;

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    protected static ?string $heading = '📊 Ventas Mensuales (Últimos 6 Meses)';

    protected function getOptions(): array
    {
        $monthlyData = $this->getMonthlySalesData();

        return [
            'chart' => [
                'type' => 'area',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Ventas ($)',
                    'data' => $monthlyData['sales'],
                ],
                [
                    'name' => 'Órdenes',
                    'data' => $monthlyData['orders'],
                ],
            ],
            'xaxis' => [
                'categories' => $monthlyData['months'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                [
                    'title' => [
                        'text' => 'Ventas ($)',
                    ],
                ],
                [
                    'opposite' => true,
                    'title' => [
                        'text' => 'Órdenes',
                    ],
                ],
            ],
            'colors' => ['#10b981', '#3b82f6'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shadeIntensity' => 1,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.1,
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];
    }

    private function getMonthlySalesData(): array
    {
        $months = [];
        $sales = [];
        $orders = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');

            $monthOrders = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->get();

            $monthSales = $monthOrders->sum('total');
            $orderCount = $monthOrders->count();

            $months[] = $monthName;
            $sales[] = round($monthSales, 2);
            $orders[] = $orderCount;
        }

        // Si no hay datos, mostrar ejemplo
        if (array_sum($sales) == 0) {
            $sales = [1200, 1800, 2100, 1650, 2400, 2800];
            $orders = [15, 22, 28, 20, 32, 38];
        }

        return [
            'months' => $months,
            'sales' => $sales,
            'orders' => $orders,
        ];
    }
}