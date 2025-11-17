<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationLabel = 'Productos';

    protected static ?string $modelLabel = 'Producto';

    protected static ?string $pluralModelLabel = 'Productos';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catálogo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Datos básicos del producto')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set, Forms\Get $get) {
                                if ($operation === 'create' || $operation === 'edit') {
                                    $brandId = $get('brand_id');
                                    if ($brandId && $state) {
                                        $brand = Brand::find($brandId);
                                        if ($brand) {
                                            $set('slug', Str::slug($brand->name . ' ' . $state));
                                        }
                                    }
                                }
                            })
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255)
                            ->placeholder('Código único del producto')
                            ->columnSpan(1),
                        Forms\Components\Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),
                            ])
                            ->columnSpan(1),
                        Forms\Components\Select::make('brand_id')
                            ->label('Marca')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set, Forms\Get $get) {
                                if ($operation === 'create' || $operation === 'edit') {
                                    $name = $get('name');
                                    if ($state && $name) {
                                        $brand = Brand::find($state);
                                        if ($brand) {
                                            $set('slug', Str::slug($brand->name . ' ' . $name));
                                        }
                                    }
                                }
                            })
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),
                            ])
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL amigable)')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->helperText('Se genera automáticamente desde la marca y nombre del producto.')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('enableEdit')
                                    ->label('Editar')
                                    ->icon('heroicon-m-pencil')
                                    ->action(function ($component) {
                                        $component->disabled(false);
                                    })
                            )
                            ->columnSpan(1),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Descripción detallada del producto...')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Precios e Inventario')
                    ->description('Gestión de precios y stock')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Precio Regular')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('discount_price')
                            ->label('Precio con Descuento')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->helperText('Dejar vacío si no hay descuento')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stock Disponible')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('unidades')
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Producto Activo')
                            ->helperText('Solo los productos activos se muestran en la tienda')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Imagen Principal')
                    ->description('Imagen destacada del producto')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagen')
                            ->image()
                            ->directory('product-images')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                                '16:9',
                            ])
                            ->maxSize(2048)
                            ->rules(['mimes:jpeg,jpg,png,gif,webp'])
                            ->helperText('Tamaño máximo: 2MB. Formatos: JPEG, PNG, GIF, WebP')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-product.png')),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Product $record): string => $record->sku ? "SKU: {$record->sku}" : ''),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marca')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('ARS')
                    ->sortable()
                    ->description(fn (Product $record): ?string =>
                        $record->discount_price ? "Antes: $" . number_format($record->price, 2) : null
                    ),
                Tables\Columns\TextColumn::make('discount_price')
                    ->label('Precio Oferta')
                    ->money('ARS')
                    ->color('success')
                    ->weight('bold')
                    ->toggleable()
                    ->placeholder('Sin oferta'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (int $state): string => $state . ' unidades')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Marca')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
                Tables\Filters\Filter::make('stock_bajo')
                    ->label('Stock bajo (< 10)')
                    ->query(fn ($query) => $query->where('stock', '<', 10)),
                Tables\Filters\Filter::make('sin_stock')
                    ->label('Sin stock')
                    ->query(fn ($query) => $query->where('stock', 0)),
                Tables\Filters\Filter::make('con_descuento')
                    ->label('Con descuento')
                    ->query(fn ($query) => $query->whereNotNull('discount_price')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activar')
                        ->label('Activar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductPhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
