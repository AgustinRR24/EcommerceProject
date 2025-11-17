<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartItemResource\Pages;
use App\Filament\Resources\CartItemResource\RelationManagers;
use App\Models\CartItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CartItemResource extends Resource
{
    protected static ?string $model = CartItem::class;

    protected static ?string $navigationLabel = 'Artículos del Carrito';

    protected static ?string $modelLabel = 'Artículo del Carrito';

    protected static ?string $pluralModelLabel = 'Artículos del Carrito';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Artículo')
                    ->description('Detalles del producto en el carrito')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Cliente')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\Select::make('product_id')
                            ->label('Producto')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->suffix('unidades')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('price')
                            ->label('Precio Unitario')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->helperText('Precio al momento de agregar al carrito')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (CartItem $record): string => $record->user->email ?? ''),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->description(fn (CartItem $record): ?string =>
                        $record->product ? "SKU: {$record->product->sku}" : null
                    ),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->suffix(' unidades'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio Unit.')
                    ->money('ARS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('ARS')
                    ->sortable()
                    ->weight('bold')
                    ->getStateUsing(fn ($record) => $record->quantity * $record->price),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Agregado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn (CartItem $record): string => $record->created_at->diffForHumans()),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Cliente')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Producto')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('cantidad_alta')
                    ->label('Cantidad alta (>= 5)')
                    ->query(fn ($query) => $query->where('quantity', '>=', 5)),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Agregado desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Agregado hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['hasta'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->requiresConfirmation(),
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
            'index' => Pages\ListCartItems::route('/'),
            'create' => Pages\CreateCartItem::route('/create'),
            'edit' => Pages\EditCartItem::route('/{record}/edit'),
        ];
    }
}
