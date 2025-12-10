<?php

namespace App\Filament\Widgets;

use App\Models\Categorie;
use App\Models\OrderDetail;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CollectByProductTable extends BaseWidget
{
    protected static ?int $sort = 10;
    protected static ?string $heading = '💰 Recaudación por Producto';
    protected static ?string $pollingInterval = '10s';

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderDetail::query()
                    ->join('products', 'order_details.product_id', '=', 'products.id')
                    ->join('orders', 'order_details.order_id', '=', 'orders.id')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->selectRaw('
                        products.id,
                        products.name as product_name,
                        categories.name as category_name,
                        products.price as unit_price,
                        SUM(order_details.quantity) as total_quantity,
                        SUM(order_details.quantity * order_details.price) as total_revenue,
                        COUNT(DISTINCT orders.id) as order_count
                    ')
                    ->groupBy('products.id', 'products.name', 'categories.name', 'products.price')
            )
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('category_name')
                    ->label('Categoría')
                    ->badge()
                    ->color('info')
                    ->default('Sin categoría'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio Unitario')
                    ->money('ARS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Cantidad Vendida')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Recaudación Total')
                    ->money('ARS')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\TextColumn::make('order_count')
                    ->label('Órdenes')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(
                        Categorie::pluck('name', 'name')->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && $data['value']) {
                            return $query->where('categories.name', $data['value']);
                        }
                        return $query;
                    }),

                Tables\Filters\Filter::make('revenue_range')
                    ->label('Rango de Recaudación')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('revenue_from')
                                    ->label('Desde')
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('0.00'),
                                Forms\Components\TextInput::make('revenue_to')
                                    ->label('Hasta')
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('999999.99'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                isset($data['revenue_from']) && $data['revenue_from'],
                                fn (Builder $query) => $query->havingRaw('SUM(order_details.quantity * order_details.price) >= ?', [$data['revenue_from']])
                            )
                            ->when(
                                isset($data['revenue_to']) && $data['revenue_to'],
                                fn (Builder $query) => $query->havingRaw('SUM(order_details.quantity * order_details.price) <= ?', [$data['revenue_to']])
                            );
                    }),

                Tables\Filters\Filter::make('quantity_range')
                    ->label('Rango de Cantidad')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('quantity_from')
                                    ->label('Desde')
                                    ->numeric()
                                    ->placeholder('0'),
                                Forms\Components\TextInput::make('quantity_to')
                                    ->label('Hasta')
                                    ->numeric()
                                    ->placeholder('9999'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                isset($data['quantity_from']) && $data['quantity_from'],
                                fn (Builder $query) => $query->havingRaw('SUM(order_details.quantity) >= ?', [$data['quantity_from']])
                            )
                            ->when(
                                isset($data['quantity_to']) && $data['quantity_to'],
                                fn (Builder $query) => $query->havingRaw('SUM(order_details.quantity) <= ?', [$data['quantity_to']])
                            );
                    }),

                Tables\Filters\Filter::make('date_range')
                    ->label('Rango de Fechas')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('created_from')
                                    ->label('Desde')
                                    ->placeholder('Fecha inicial'),
                                Forms\Components\DatePicker::make('created_until')
                                    ->label('Hasta')
                                    ->placeholder('Fecha final'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                isset($data['created_from']) && $data['created_from'],
                                fn (Builder $query) => $query->whereDate('orders.created_at', '>=', $data['created_from'])
                            )
                            ->when(
                                isset($data['created_until']) && $data['created_until'],
                                fn (Builder $query) => $query->whereDate('orders.created_at', '<=', $data['created_until'])
                            );
                    }),

                Tables\Filters\TernaryFilter::make('high_revenue')
                    ->label('Alta Recaudación')
                    ->placeholder('Todos los productos')
                    ->trueLabel('Más de $100')
                    ->falseLabel('Menos de $100')
                    ->queries(
                        true: fn (Builder $query) => $query->havingRaw('SUM(order_details.quantity * order_details.price) > ?', [100]),
                        false: fn (Builder $query) => $query->havingRaw('SUM(order_details.quantity * order_details.price) <= ?', [100]),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->defaultSort('total_revenue', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
