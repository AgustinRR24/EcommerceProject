<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CollectByCustomerTable extends BaseWidget
{
    protected static ?int $sort = 11;
    protected static ?string $heading = '👥 Compras por Cliente';
    protected static ?string $pollingInterval = '10s';

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->join('users', 'orders.user_id', '=', 'users.id')
                    ->selectRaw('
                        users.id,
                        users.name,
                        users.email,
                        COUNT(DISTINCT orders.id) as total_orders,
                        SUM(orders.total) as total_spent,
                        AVG(orders.total) as avg_order_value,
                        MAX(orders.created_at) as last_order_date,
                        MIN(orders.created_at) as first_order_date
                    ')
                    ->groupBy('users.id', 'users.name', 'users.email')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record): string => $record->email),
                Tables\Columns\TextColumn::make('total_orders')
                    ->label('Total Órdenes')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-shopping-bag'),
                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Gastado')
                    ->money('ARS')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('ARS')
                            ->label('Total Recaudado'),
                    ]),
                Tables\Columns\TextColumn::make('avg_order_value')
                    ->label('Promedio por Orden')
                    ->money('ARS')
                    ->sortable()
                    ->color('warning')
                    ->summarize([
                        Tables\Columns\Summarizers\Average::make()
                            ->money('ARS')
                            ->label('Promedio General'),
                    ]),
                Tables\Columns\TextColumn::make('first_order_date')
                    ->label('Primera Compra')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->description(fn ($record): string => $record->first_order_date ? 'Hace ' . \Carbon\Carbon::parse($record->first_order_date)->diffForHumans() : ''),
                Tables\Columns\TextColumn::make('last_order_date')
                    ->label('Última Compra')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record): string => \Carbon\Carbon::parse($record->last_order_date)->diffInDays(now()) < 30 ? 'success' : 'warning')
                    ->description(fn ($record): string => $record->last_order_date ? 'Hace ' . \Carbon\Carbon::parse($record->last_order_date)->diffForHumans() : ''),
            ])
            ->filters([
                Tables\Filters\Filter::make('spent_range')
                    ->label('Rango de Gasto Total')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('spent_from')
                                    ->label('Desde')
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('0.00'),
                                Forms\Components\TextInput::make('spent_to')
                                    ->label('Hasta')
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('999999.99'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                isset($data['spent_from']) && $data['spent_from'],
                                fn (Builder $query) => $query->havingRaw('SUM(orders.total) >= ?', [$data['spent_from']])
                            )
                            ->when(
                                isset($data['spent_to']) && $data['spent_to'],
                                fn (Builder $query) => $query->havingRaw('SUM(orders.total) <= ?', [$data['spent_to']])
                            );
                    }),

                Tables\Filters\Filter::make('orders_range')
                    ->label('Cantidad de Órdenes')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('orders_from')
                                    ->label('Desde')
                                    ->numeric()
                                    ->placeholder('1'),
                                Forms\Components\TextInput::make('orders_to')
                                    ->label('Hasta')
                                    ->numeric()
                                    ->placeholder('100'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                isset($data['orders_from']) && $data['orders_from'],
                                fn (Builder $query) => $query->havingRaw('COUNT(DISTINCT orders.id) >= ?', [$data['orders_from']])
                            )
                            ->when(
                                isset($data['orders_to']) && $data['orders_to'],
                                fn (Builder $query) => $query->havingRaw('COUNT(DISTINCT orders.id) <= ?', [$data['orders_to']])
                            );
                    }),

                Tables\Filters\Filter::make('last_order_date')
                    ->label('Última Compra')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('last_order_from')
                                    ->label('Desde')
                                    ->placeholder('Fecha inicial'),
                                Forms\Components\DatePicker::make('last_order_until')
                                    ->label('Hasta')
                                    ->placeholder('Fecha final'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                isset($data['last_order_from']) && $data['last_order_from'],
                                fn (Builder $query) => $query->havingRaw('MAX(orders.created_at) >= ?', [$data['last_order_from']])
                            )
                            ->when(
                                isset($data['last_order_until']) && $data['last_order_until'],
                                fn (Builder $query) => $query->havingRaw('MAX(orders.created_at) <= ?', [$data['last_order_until']])
                            );
                    }),

                Tables\Filters\TernaryFilter::make('high_value')
                    ->label('Clientes VIP')
                    ->placeholder('Todos los clientes')
                    ->trueLabel('Más de $50 gastados')
                    ->falseLabel('Menos de $50 gastados')
                    ->queries(
                        true: fn (Builder $query) => $query->havingRaw('SUM(orders.total) > ?', [50]),
                        false: fn (Builder $query) => $query->havingRaw('SUM(orders.total) <= ?', [50]),
                        blank: fn (Builder $query) => $query,
                    ),

                Tables\Filters\TernaryFilter::make('recent_customers')
                    ->label('Clientes Recientes')
                    ->placeholder('Todos')
                    ->trueLabel('Últimos 30 días')
                    ->falseLabel('Más de 30 días')
                    ->queries(
                        true: fn (Builder $query) => $query->havingRaw('MAX(orders.created_at) >= ?', [now()->subDays(30)]),
                        false: fn (Builder $query) => $query->havingRaw('MAX(orders.created_at) < ?', [now()->subDays(30)]),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->defaultSort('total_spent', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
