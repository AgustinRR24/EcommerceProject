<?php

namespace App\Filament\Customers\Resources;

use App\Filament\Customers\Resources\OrderResource\Pages;
use App\Filament\Customers\Resources\OrderResource\RelationManagers\OrderDetailsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationLabel = 'Órdenes';

    protected static ?string $modelLabel = 'Orden';

    protected static ?string $pluralModelLabel = 'Órdenes';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('order_number')
                    ->label('Número de Orden')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->label('Estado')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('promo_code_id')
                    ->label('Código Promocional')
                    ->relationship('promoCode', 'id')
                    ->default(null),
                Forms\Components\TextInput::make('discount')
                    ->label('Descuento')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('tax')
                    ->label('Impuesto')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_method')
                    ->label('Método de Pago')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_status')
                    ->label('Estado de Pago')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\TextInput::make('shipping_address')
                    ->label('Dirección de Envío')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_city')
                    ->label('Ciudad')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_state')
                    ->label('Provincia/Estado')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_country')
                    ->label('País')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_zipcode')
                    ->label('Código Postal')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_phone')
                    ->label('Teléfono')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Número de Orden')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Número de orden copiado')
                    ->weight('bold'),
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
                    ]),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Estado de Pago')
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
                    ]),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('ARS')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método de Pago')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'mercadopago' => 'MercadoPago',
                        'cash' => 'Efectivo',
                        'credit_card' => 'Tarjeta de Crédito',
                        'debit_card' => 'Tarjeta de Débito',
                        default => ucfirst($state),
                    })
                    ->icon('heroicon-o-credit-card')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn (Order $record): string => $record->created_at->diffForHumans()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'processing' => 'Procesando',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Estado de Pago')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        'cancelled' => 'Cancelado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver Detalles')
                    ->modalHeading(fn (Order $record): string => "Orden #{$record->order_number}")
                    ->modalContent(fn (Order $record): \Illuminate\View\View => view(
                        'filament.customers.resources.order.view-order',
                        ['record' => $record]
                    ))
                    ->modalWidth('3xl')
                    ->color('info'),
            ])
            ->bulkActions([
                // Los clientes no deberían poder eliminar órdenes
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function getRelations(): array
    {
        return [
            OrderDetailsRelationManager::class
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
