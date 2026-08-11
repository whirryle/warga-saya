<?php

namespace App\Filament\Resources;

use Filament\Panel;
use Filament\Forms;
use Filament\Tables;
use App\Models\Civilian;
use Filament\Forms\Form;
use App\Models\Liability;
use Filament\Tables\Table;
use App\Models\DataTanggungan;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DataTanggunganResource\Pages;
use App\Filament\Resources\DataTanggunganResource\RelationManagers;
use Illuminate\Support\Facades\Auth;

class DataTanggunganResource extends Resource
{
    protected static ?string $model = Liability::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Data Tanggungan';
    protected static ?string $label = 'Data Tanggungan';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('full_name')
                    ->required()
                    ->label('Nama tanggungan'),
                DatePicker::make('born_date')
                    ->required()
                    ->label('Tanggal lahir'),
                Select::make('gender')
                    ->required()
                    ->label('Jenis kelamin')
                    ->options([
                        "Pria" => "Pria",
                        "Wanita" => "Wanita"
                    ]),
                Select::make('last_education')
                    ->required()
                    ->label('Pendidikan terakhir')
                    ->options([
                        "SMA sederajat" => "SMA sederajat",
                        "D3" => "D3",
                        "D4/S1" => "D4/S1",
                        "S2" => "S2",
                        "S3" => "S3",

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama lengkap'),
                TextColumn::make('born_date')
                    ->label('Umur')
                    ->formatStateUsing(function ($state, Liability $liability) {
                        return Carbon::parse($liability->born_date)->age . ' ' . 'tahun';
                    }),
                TextColumn::make('gender')
                    ->label('Jenis kelamin')
                    ->formatStateUsing(function ($state, Liability $liability) {
                        if ($liability->gender == false) {
                            return 'Pria';
                        }
                        return 'Wanita';
                    }),
                TextColumn::make('last_education')
                    ->label('Pendidikan terakhir'),
                TextColumn::make('civilian.full_name')
                    ->label('Tanggungan dari...')

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListDataTanggungans::route('/'),
            // 'create' => Pages\CreateDataTanggungan::route('/create'),
            // 'edit' => Pages\EditDataTanggungan::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // super admin dapat melihat menu ini (Data Tanggungan)
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->isSuperAdmin() ?? false;
    }
}
