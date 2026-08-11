<?php

namespace App\Filament\Resources;

use Filament\Panel;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CivilianJob;
use App\Models\DataPekerjaan;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DataPekerjaanResource\Pages;
use App\Filament\Resources\DataPekerjaanResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;

class DataPekerjaanResource extends Resource
{
    protected static ?string $model = CivilianJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Data Pekerjaan';
    protected static ?string $label = 'Data Pekerjaan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('job_Place')
                    ->label('Nama tempat kerja')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('job_place')
                    ->label('Tempat kerja'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListDataPekerjaans::route('/'),
            // 'create' => Pages\CreateDataPekerjaan::route('/create'),
            // 'edit' => Pages\EditDataPekerjaan::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // super admin dapat melihat menu ini (Data Pekerjaan)
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->isSuperAdmin() ?? false;
    }
}
