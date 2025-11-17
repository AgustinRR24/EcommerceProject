<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Filament\Resources\PromoCodeResource\RelationManagers;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationLabel = 'Códigos Promocionales';

    protected static ?string $modelLabel = 'Código Promocional';

    protected static ?string $pluralModelLabel = 'Códigos Promocionales';

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Código Promocional')
                    ->description('Configure el código y descuento')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Código Promocional')
                            ->required()
                            ->maxLength(255)
                            ->unique(PromoCode::class, 'code', ignoreRecord: true)
                            ->placeholder('DESCUENTO2024')
                            ->helperText('Código que los clientes usarán en el checkout')
                            ->columnSpan(2),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Código Activo')
                            ->helperText('Solo los códigos activos pueden ser utilizados')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('discount_percentage')
                            ->label('Porcentaje de Descuento')
                            ->required()
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->helperText('Porcentaje de descuento a aplicar (0-100)')
                            ->columnSpan(3),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Código copiado')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label('Descuento')
                    ->numeric()
                    ->sortable()
                    ->suffix('%')
                    ->badge()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 50 => 'success',
                        $state >= 25 => 'warning',
                        default => 'info',
                    })
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Usos')
                    ->counts('orders')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->description(fn (PromoCode $record): string =>
                        $record->orders_count > 0 ? 'veces utilizado' : 'nunca usado'
                    ),
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
                    ->placeholder('Todos los códigos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
                Tables\Filters\Filter::make('descuento_alto')
                    ->label('Descuento alto (>= 50%)')
                    ->query(fn ($query) => $query->where('discount_percentage', '>=', 50)),
                Tables\Filters\Filter::make('nunca_usado')
                    ->label('Nunca usado')
                    ->query(fn ($query) => $query->doesntHave('orders')),
                Tables\Filters\Filter::make('usado')
                    ->label('Ya utilizado')
                    ->query(fn ($query) => $query->has('orders')),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
