<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class OrderStatusChart extends ApexChartWidget
{
    protected static ?string $chartId = 'orderStatusChart';
    protected static ?int $sort = 8;
    protected static ?string $pollingInterval = '10s';

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
            'colors' => $statusData['colors'],
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
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            dataLabels: {
                enabled: true,
                formatter: function (val, opt) {
                    return Math.round(val) + '%'
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' órdenes'
                    }
                }
            }
        }
        JS);
    }

    private function getOrderStatusData(): array
    {
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [];
        $values = [];
        $colors = [];

        $statusMapping = [
            'completed' => ['label' => 'Completadas', 'color' => '#10b981'],
            'pending' => ['label' => 'Pendientes', 'color' => '#f59e0b'],
            'cancelled' => ['label' => 'Canceladas', 'color' => '#ef4444'],
            'processing' => ['label' => 'En Proceso', 'color' => '#6b7280'],
        ];

        foreach ($statusMapping as $key => $config) {
            if (isset($statusCounts[$key])) {
                $labels[] = $config['label'];
                $values[] = (int) $statusCounts[$key];
                $colors[] = $config['color'];
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => $colors,
        ];
    }
}