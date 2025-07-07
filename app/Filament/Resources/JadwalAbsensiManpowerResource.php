<?php
namespace App\Filament\Resources;

use App\Filament\Resources\JadwalAbsensiManpowerResource\Pages;
use App\Models\JadwalAbsensiManpower;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use App\Imports\JadwalAbsensiImport;
use App\Exports\JadwalAbsensiTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class JadwalAbsensiManpowerResource extends Resource
{
    protected static ?string $model = JadwalAbsensiManpower::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Jadwal Absensi';

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
                Forms\Components\TimePicker::make('jam_check_in')
                    ->required(),
                Forms\Components\TimePicker::make('jam_check_out')
                    ->required(),
                Forms\Components\TextInput::make('latitude')
                    ->required()
                    ->numeric()
                    ->step(0.00000001),
                Forms\Components\TextInput::make('longitude')
                    ->required()
                    ->numeric()
                    ->step(0.00000001),
                Forms\Components\TextInput::make('radius_meter')
                    ->required()
                    ->numeric()
                    ->default(100)
                    ->suffix('meter'),
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
                Tables\Columns\TextColumn::make('jam_check_in'),
                Tables\Columns\TextColumn::make('jam_check_out'),
                Tables\Columns\TextColumn::make('latitude')
                    ->limit(10),
                Tables\Columns\TextColumn::make('longitude')
                    ->limit(10),
                Tables\Columns\TextColumn::make('radius_meter')
                    ->suffix(' m'),
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
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
               Tables\Actions\Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required()
                            ->helperText('Upload file Excel (.xlsx atau .xls)')
                            ->disk('public')
                            ->directory('imports'),
                    ])
                    ->action(function (array $data) {
                        $file = public_path('/storage/' . $data['file']);
                        
                        try {
                            $import = new JadwalAbsensiImport();
                            Excel::import($import, $file);
                            
                            $errors = $import->getErrors();
                            
                            if (empty($errors)) {
                                Notification::make()
                                    ->title('Import Berhasil')
                                    ->success()
                                    ->send();
                            } else {
                                $errorMessage = implode('<br>', $errors);
                                Notification::make()
                                    ->title('Import Selesai dengan Peringatan')
                                    ->body($errorMessage)
                                    ->warning()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Tables\Actions\Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        return Excel::download(new JadwalAbsensiTemplateExport(), 'template_jadwal_absensi.xlsx');
                    }),
                    
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (): string => route('jadwal.export')),
            ])
;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalAbsensiManpowers::route('/'),
            'create' => Pages\CreateJadwalAbsensiManpower::route('/create'),
            'edit' => Pages\EditJadwalAbsensiManpower::route('/{record}/edit'),
        ];
    }
}