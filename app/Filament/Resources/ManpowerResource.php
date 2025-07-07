<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ManpowerResource\Pages;
use App\Models\Manpower;
use App\Models\Projek;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

use App\Imports\ManpowerImport;
use App\Exports\ManpowerTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ManpowerResource extends Resource
{
    protected static ?string $model = Manpower::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manpower';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nip')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
                Forms\Components\Select::make('projek_id')
                    ->relationship('projek', 'nama_projek')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $projek = Projek::find($state);
                        if ($projek) {
                            $set('kode_projek', $projek->kode_projek);
                        }
                    }),
                Forms\Components\TextInput::make('kode_projek')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\Select::make('role_id')
                    ->relationship('role', 'nama_role')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->formatStateUsing(fn (string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                Tables\Columns\TextColumn::make('projek.nama_projek')
                    ->sortable(),
                Tables\Columns\TextColumn::make('role.nama_role')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('projek_id')
                    ->relationship('projek', 'nama_projek'),
                Tables\Filters\SelectFilter::make('role_id')
                    ->relationship('role', 'nama_role'),
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
                            $import = new ManpowerImport();
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
                        return Excel::download(new ManpowerTemplateExport(), 'template_manpower.xlsx');
                    }),
                    
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (): string => route('manpower.export')),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManpowers::route('/'),
            'create' => Pages\CreateManpower::route('/create'),
            'edit' => Pages\EditManpower::route('/{record}/edit'),
        ];
    }
}