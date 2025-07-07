<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjekResource\Pages;
use App\Models\Projek;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use App\Imports\ProjekImport;
use App\Exports\ProjekTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;


class ProjekResource extends Resource
{
    protected static ?string $model = Projek::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Projek';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_projek')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Projek'),

                Forms\Components\FileUpload::make('logo_projek')
                    ->label('Logo Projek')
                    ->disk('public') // Tambahkan ini
                    ->directory('logo-projek')
                    ->image()
                    ->imageEditor() // Enable image editor
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->maxSize(5120) // 5MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                    ->downloadable() // Allow download
                    ->previewable() // Enable preview
                    ->helperText('Upload logo projek (JPG, PNG, WEBP - Max 5MB)'),

                Forms\Components\TextInput::make('kode_projek')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Kode Projek')
                    ->placeholder('Contoh: PRJ001'),

                Forms\Components\TextInput::make('nama_lengkap_pic')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Lengkap PIC')
                    ->placeholder('Person In Charge'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_projek')
                    ->label('Logo')
                    ->disk('public') // Tambahkan ini
                    ->size(60) // Ukuran gambar
                    ->circular() // Bentuk bulat (opsional)
                    ->defaultImageUrl(url('/images/no-image.png')), // Placeholder jika tidak ada gambar

                Tables\Columns\TextColumn::make('nama_projek')
                    ->label('Nama Projek')
                    ->searchable()
                    ->sortable()
                    ->wrap(), // Allow text wrapping

                Tables\Columns\TextColumn::make('kode_projek')
                    ->label('Kode Projek')
                    ->searchable()
                    ->sortable()
                    ->badge() // Display as badge
                    ->color('primary'),

                Tables\Columns\TextColumn::make('nama_lengkap_pic')
                    ->label('PIC')
                    ->searchable()
                    ->sortable()
                    ->limit(30), // Limit characters displayed

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc') // Default sort by newest
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
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
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Projek')
                    ->modalDescription('Apakah Anda yakin ingin menghapus projek ini? Data yang terhapus tidak dapat dikembalikan.')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Projek yang Dipilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus projek yang dipilih? Data yang terhapus tidak dapat dikembalikan.')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ])
            ->headerActions([
        Tables\Actions\CreateAction::make()
            ->label('Tambah Projek'),
            
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
                // dd($file);
                
                try {
                    $import = new ProjekImport();
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
                return Excel::download(new ProjekTemplateExport(), 'template_projek.xlsx');
            }),
            
        Tables\Actions\Action::make('export')
            ->label('Export Excel')
            ->icon('heroicon-o-document-arrow-down')
            ->url(fn (): string => route('projek.export'))
            ->openUrlInNewTab(),
    ])
            ->emptyStateHeading('Belum ada projek')
            ->emptyStateDescription('Mulai dengan membuat projek pertama Anda.')
            ->emptyStateIcon('heroicon-o-building-office');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjeks::route('/'),
            'create' => Pages\CreateProjek::route('/create'),
            'edit' => Pages\EditProjek::route('/{record}/edit'),
            // 'view' => Pages\ViewProjek::route('/{record}'), // Tambahkan view page
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }
}

// Jangan lupa untuk membuat ViewProjek page jika belum ada:
/*
php artisan make:filament-page ViewProjek --resource=ProjekResource --type=ViewRecord
*/