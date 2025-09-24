<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TopProductsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'topProductsChart';
    protected static ?int $sort = 7;

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    protected static ?string $heading = '🏆 Top 5 Productos Más Vendidos';

    protected function getOptions(): array
    {
        $topProducts = $this->getTopProductsData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Cantidad Vendida',
                    'data' => $topProducts['quantities'],
                ],
            ],
            'xaxis' => [
                'categories' => $topProducts['names'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Unidades Vendidas',
                ],
            ],
            'colors' => ['#3b82f6'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'horizontal' => false,
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
        ];
    }

    private function getTopProductsData(): array
    {
        $topProducts = OrderDetail::selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->with('product')
            ->get();

        $names = [];
        $quantities = [];

        foreach ($topProducts as $item) {
            if ($item->product) {
                $names[] = substr($item->product->name, 0, 20) . '...';
                $quantities[] = (int) $item->total_sold;
            }
        }

        // Si no hay datos, mostrar ejemplo
        if (empty($names)) {
            $names = ['iPhone 14', 'MacBook Pro', 'AirPods', 'iPad Air', 'Apple Watch'];
            $quantities = [25, 18, 32, 15, 28];
        }

        return [
            'names' => $names,
            'quantities' => $quantities,
        ];
    }
}