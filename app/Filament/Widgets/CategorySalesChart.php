<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use App\Models\Categorie;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CategorySalesChart extends ApexChartWidget
{
    protected static ?string $chartId = 'categorySalesChart';
    protected static ?int $sort = 9;

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
                    'name' => 'Ingresos ($)',
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
            'dataLabels' => [
                'enabled' => true,
            ],
        ];
    }

    private function getCategorySalesData(): array
    {
        // Primero intentar obtener categorías reales de la BD
        $categories = \App\Models\Categorie::take(5)->get();

        $names = [];
        $sales = [];

        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                // Asegurar que el nombre no esté vacío
                $categoryName = !empty($category->name) ? $category->name : 'Sin nombre';
                $names[] = $categoryName;
                // Simular ventas por categoría (puedes cambiar esto por lógica real)
                $sales[] = rand(1500, 6000);
            }
        } else {
            // Datos de ejemplo si no hay categorías
            $names = ['Electrónicos', 'Ropa', 'Hogar', 'Deportes', 'Libros'];
            $sales = [5200, 3800, 2900, 2100, 1500];
        }

        // Debug: forzar datos si están vacíos
        if (empty($names) || count($names) === 0) {
            $names = ['Electrónicos', 'Ropa', 'Hogar', 'Deportes', 'Libros'];
            $sales = [5200, 3800, 2900, 2100, 1500];
        }

        return [
            'names' => $names,
            'sales' => $sales,
        ];
    }
}