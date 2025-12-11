<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use App\Services\ReportService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;


class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Reportes';

    protected static ?string $modelLabel = 'Reporte';

    protected static ?string $pluralModelLabel = 'Reportes';

    protected static ?string $navigationGroup = 'Ventas';

    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Reporte')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Reporte')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Reporte Diciembre 2024')
                            ->columnSpan(2),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha Desde')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Opcional'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha Hasta')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Opcional'),
                        Forms\Components\Select::make('user_id')
                            ->label('Generado por')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(Auth::user()->id)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'generated' => 'Generado',
                                'failed' => 'Fallido',
                            ])
                            ->required()
                            ->default('generated'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->placeholder('Notas adicionales sobre este reporte')
                            ->columnSpanFull()
                            ->rows(3),
                    ])
                    ->columns(2),
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
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Desde')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Todos'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Hasta')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Todos'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Generado por')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'generated' => 'Generado',
                        'failed' => 'Fallido',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'generated',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'generated' => 'Generado',
                        'failed' => 'Fallido',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Creado desde')
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Creado hasta')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Html2MediaAction::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->savePdf()
                    ->requiresConfirmation(false)
                    ->filename(fn($record) => 'reporte-' . str_replace(' ', '-', strtolower($record->name)) . '-' . now()->format('Ymd-His'))
                    ->content(function ($record) {
                        // Check if PDF already exists and return stored content
                        if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                            return Storage::disk('public')->get($record->file_path);
                        }

                        // Generate new PDF content
                        $data = ReportService::generateReport(
                            $record->start_date?->format('Y-m-d'),
                            $record->end_date?->format('Y-m-d')
                        );
                        $data['generated_at'] = now()->format('d/m/Y H:i:s');

                        // Save file path to database for future use
                        $filename = 'reporte-' . str_replace(' ', '-', strtolower($record->name)) . '-' . now()->format('Ymd-His') . '.pdf';
                        $filePath = 'reports/' . $filename;

                        $record->update([
                            'file_path' => $filePath,
                            'status' => 'generated'
                        ]);

                        return view('reports.pdf', ['data' => $data]);
                    })
                    ->after(function ($record) {
                        // This will be called after PDF generation
                        // The PDF is already saved by Html2MediaAction, we just update the record
                        if (!$record->file_path) {
                            $filename = 'reporte-' . str_replace(' ', '-', strtolower($record->name)) . '-' . now()->format('Ymd-His') . '.pdf';
                            $record->update([
                                'file_path' => 'reports/' . $filename,
                                'status' => 'generated'
                            ]);
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
