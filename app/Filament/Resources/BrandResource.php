<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationLabel = 'Marcas';

    protected static ?string $modelLabel = 'Marca';

    protected static ?string $pluralModelLabel = 'Marcas';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Catálogo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Marca')
                    ->description('Datos básicos de la marca')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            )
                            ->columnSpan(2),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Marca Activa')
                            ->helperText('Solo las marcas activas se muestran en la tienda')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL amigable)')
                            ->required()
                            ->maxLength(255)
                            ->unique(Brand::class, 'slug', ignoreRecord: true)
                            ->helperText('Se genera automáticamente desde el nombre de la marca.')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('enableEdit')
                                    ->label('Editar')
                                    ->icon('heroicon-m-pencil')
                                    ->action(function ($component) {
                                        $component->disabled(false);
                                    })
                            )
                            ->columnSpan(3),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Logo de la Marca')
                    ->description('Imagen o URL del logo')
                    ->schema([
                        Forms\Components\TextInput::make('logo')
                            ->label('URL del Logo')
                            ->maxLength(255)
                            ->placeholder('https://ejemplo.com/logo.png')
                            ->helperText('Ingrese la URL del logo de la marca')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Brand $record): string => $record->slug),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Productos')
                    ->counts('products')
                    ->badge()
                    ->color('info')
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
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todas las marcas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
                Tables\Filters\Filter::make('con_productos')
                    ->label('Con productos')
                    ->query(fn ($query) => $query->has('products')),
                Tables\Filters\Filter::make('sin_productos')
                    ->label('Sin productos')
                    ->query(fn ($query) => $query->doesntHave('products')),
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
                        ->label('Activar seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('name', 'asc');
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
