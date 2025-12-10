<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use App\Models\Categorie;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CategorySalesChart extends ApexChartWidget
{
    protected static ?string $chartId = 'categorySalesChart';
    protected static ?int $sort = 9;
    protected static ?string $pollingInterval = '10s';

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    protected static ?string $heading = '🏷️ Ventas por Categoría';

    protected function getOptions(): array
    {
        $categoryData = $this->getCategorySalesData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Ingresos',
                    'data' => $categoryData['sales'],
                ],
            ],
            'xaxis' => [
                'categories' => $categoryData['names'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Ingresos ($)',
                ],
                'labels' => [
                    'style' => [
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'horizontal' => true,
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
                formatter: function (val) {
                    return '$' + val.toFixed(2)
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '$' + val.toFixed(2)
                    }
                }
            }
        }
        JS);
    }

    private function getCategorySalesData(): array
    {
        // Obtener ventas reales por categoría desde la base de datos
        $categorySales = OrderDetail::join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->selectRaw('categories.name, SUM(order_details.quantity * order_details.price) as total_sales')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sales', 'desc')
            ->limit(10)
            ->get();

        $names = [];
        $sales = [];

        if ($categorySales->count() > 0) {
            foreach ($categorySales as $catSale) {
                $names[] = $catSale->name;
                $sales[] = (float) $catSale->total_sales;
            }
        } else {
            // Si no hay ventas, mostrar todas las categorías con 0
            $allCategories = Categorie::take(5)->get();
            foreach ($allCategories as $category) {
                $names[] = $category->name;
                $sales[] = 0;
            }
        }

        return [
            'names' => $names,
            'sales' => $sales,
        ];
    }
}