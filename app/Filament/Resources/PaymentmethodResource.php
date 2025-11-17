<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentmethodResource\Pages;
use App\Filament\Resources\PaymentmethodResource\RelationManagers;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationLabel = 'Métodos de Pago';

    protected static ?string $modelLabel = 'Método de Pago';

    protected static ?string $pluralModelLabel = 'Métodos de Pago';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Configuración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Método de Pago')
                    ->description('Configure el método de pago disponible')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Método')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('MercadoPago, Tarjeta de Crédito, etc.')
                            ->helperText('Nombre descriptivo del método de pago')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Método')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-credit-card'),
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPaymentmethods::route('/'),
            'create' => Pages\CreatePaymentmethod::route('/create'),
            'edit' => Pages\EditPaymentmethod::route('/{record}/edit'),
        ];
    }
}
