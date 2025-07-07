<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiManpowerResource\Pages;
use App\Models\AbsensiManpower;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsensiManpowerResource extends Resource
{
    protected static ?string $model = AbsensiManpower::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Data Absensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('manpower_id')
                    ->relationship('manpower', 'nama_lengkap')
                    ->required()
                    ->searchable(),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\TimePicker::make('jam_check_in'),
                Forms\Components\TimePicker::make('jam_check_out'),
                Forms\Components\TextInput::make('latitude_check_in')
                    ->numeric()
                    ->step(0.00000001),
                Forms\Components\TextInput::make('longitude_check_in')
                    ->numeric()
                    ->step(0.00000001),
                Forms\Components\TextInput::make('latitude_check_out')
                    ->numeric()
                    ->step(0.00000001),
                Forms\Components\TextInput::make('longitude_check_out')
                    ->numeric()
                    ->step(0.00000001),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('manpower.nama_lengkap')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('manpower.nip')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_check_in')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('jam_check_out')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                Tables\Columns\IconColumn::make('status')
                    ->getStateUsing(function ($record) {
                        if ($record->jam_check_in && $record->jam_check_out) {
                            return 'complete';
                        } elseif ($record->jam_check_in) {
                            return 'partial';
                        }
                        return 'none';
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'complete' => 'heroicon-o-check-circle',
                        'partial' => 'heroicon-o-clock',
                        default => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'complete' => 'success',
                        'partial' => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('manpower_id')
                    ->relationship('manpower', 'nama_lengkap'),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari'),
                        Forms\Components\DatePicker::make('sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'complete' => 'Lengkap (Check In & Out)',
                        'partial' => 'Sebagian (Hanya Check In)',
                        'none' => 'Belum Absen',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'complete' => $query->whereNotNull('jam_check_in')->whereNotNull('jam_check_out'),
                            'partial' => $query->whereNotNull('jam_check_in')->whereNull('jam_check_out'),
                            'none' => $query->whereNull('jam_check_in'),
                            default => $query,
                        };
                    }),
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
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (): string => route('absensi.export')),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensiManpowers::route('/'),
            'create' => Pages\CreateAbsensiManpower::route('/create'),
            // 'view' => Pages\ViewAbsensiManpower::route('/{record}'),
            'edit' => Pages\EditAbsensiManpower::route('/{record}/edit'),
        ];
    }
}