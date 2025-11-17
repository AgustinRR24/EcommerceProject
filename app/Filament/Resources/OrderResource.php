<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationLabel = 'Órdenes';

    protected static ?string $modelLabel = 'Orden';

    protected static ?string $pluralModelLabel = 'Órdenes';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Orden')
                    ->description('Datos básicos de la orden')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Cliente')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('order_number')
                            ->label('Número de Orden')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->label('Estado de la Orden')
                            ->options([
                                'pending' => 'Pendiente',
                                'processing' => 'Procesando',
                                'completed' => 'Completada',
                                'cancelled' => 'Cancelada',
                            ])
                            ->required()
                            ->default('pending')
                            ->columnSpan(1),
                        Forms\Components\Select::make('payment_status')
                            ->label('Estado de Pago')
                            ->options([
                                'pending' => 'Pendiente',
                                'approved' => 'Aprobado',
                                'rejected' => 'Rechazado',
                                'cancelled' => 'Cancelado',
                            ])
                            ->required()
                            ->default('pending')
                            ->columnSpan(1),
                        Forms\Components\Select::make('payment_method')
                            ->label('Método de Pago')
                            ->options([
                                'mercadopago' => 'MercadoPago',
                                'cash' => 'Efectivo',
                                'credit_card' => 'Tarjeta de Crédito',
                                'debit_card' => 'Tarjeta de Débito',
                            ])
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Select::make('promo_code_id')
                            ->relationship('promoCode', 'code')
                            ->label('Código Promocional')
                            ->searchable()
                            ->nullable()
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Montos')
                    ->description('Detalle de precios')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('discount')
                            ->label('Descuento')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('tax')
                            ->label('Impuesto')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->extraAttributes(['class' => 'font-bold'])
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->collapsible(),

                Forms\Components\Section::make('Información de Envío')
                    ->description('Datos de entrega')
                    ->schema([
                        Forms\Components\TextInput::make('shipping_address')
                            ->label('Dirección')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('shipping_city')
                            ->label('Ciudad')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('shipping_state')
                            ->label('Provincia/Estado')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('shipping_country')
                            ->label('País')
                            ->required()
                            ->maxLength(255)
                            ->default('Argentina')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('shipping_zipcode')
                            ->label('Código Postal')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('shipping_phone')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Notas Adicionales')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Observaciones o instrucciones especiales...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Número de Orden')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Número copiado')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Order $record): string => $record->user->email ?? ''),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'processing' => 'Procesando',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'info' => 'processing',
                    ])
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Pago')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        'cancelled' => 'Cancelado',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'gray' => 'cancelled',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('ARS')
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Order $record): ?string =>
                        $record->discount > 0 ? "Descuento: $" . number_format($record->discount, 2) : null
                    ),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método de Pago')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'mercadopago' => 'MercadoPago',
                        'cash' => 'Efectivo',
                        'credit_card' => 'Tarjeta de Crédito',
                        'debit_card' => 'Tarjeta de Débito',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-credit-card')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('shipping_city')
                    ->label('Ciudad')
                    ->searchable()
                    ->toggleable()
                    ->description(fn (Order $record): string => $record->shipping_state),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn (Order $record): string => $record->created_at->diffForHumans()),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'processing' => 'Procesando',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Estado de Pago')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        'cancelled' => 'Cancelado',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'mercadopago' => 'MercadoPago',
                        'cash' => 'Efectivo',
                        'credit_card' => 'Tarjeta de Crédito',
                        'debit_card' => 'Tarjeta de Débito',
                    ]),
                Tables\Filters\Filter::make('con_descuento')
                    ->label('Con descuento')
                    ->query(fn ($query) => $query->where('discount', '>', 0)),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['hasta'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->modalHeading(fn (Order $record): string => "Orden #{$record->order_number}")
                    ->modalContent(fn (Order $record): \Illuminate\View\View => view(
                        'filament.customers.resources.order.view-order',
                        ['record' => $record]
                    ))
                    ->modalWidth('3xl'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\Action::make('print_order')
                    ->label('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('order.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('marcar_completadas')
                        ->label('Marcar como completadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'completed']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('marcar_procesando')
                        ->label('Marcar como procesando')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'processing']))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
