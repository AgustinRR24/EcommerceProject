<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class OrderStatusChart extends ApexChartWidget
{
    protected static ?string $chartId = 'orderStatusChart';
    protected static ?int $sort = 8;

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    protected static ?string $heading = '📋 Estado de Órdenes';

    protected function getOptions(): array
    {
        $statusData = $this->getOrderStatusData();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 350,
            ],
            'series' => $statusData['values'],
            'labels' => $statusData['labels'],
            'colors' => ['#10b981', '#f59e0b', '#ef4444', '#6b7280'],
            'legend' => [
                'position' => 'bottom',
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '70%',
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'formatter' => 'function(val) { return Math.round(val) + "%" }',
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function(val) { return val + " órdenes" }',
                ],
            ],
        ];
    }

    private function getOrderStatusData(): array
    {
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [];
        $values = [];

        $statusMapping = [
            'completed' => 'Completadas',
            'pending' => 'Pendientes',
            'cancelled' => 'Canceladas',
            'processing' => 'En Proceso',
        ];

        foreach ($statusMapping as $key => $label) {
            if (isset($statusCounts[$key])) {
                $labels[] = $label;
                $values[] = $statusCounts[$key];
            }
        }

        // Si no hay datos, mostrar ejemplo
        if (empty($values)) {
            $labels = ['Completadas', 'Pendientes', 'En Proceso', 'Canceladas'];
            $values = [45, 25, 20, 10];
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}