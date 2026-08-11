<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Activity;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DataAktifitas;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DataAktifitasResource\Pages;
use App\Filament\Resources\DataAktifitasResource\RelationManagers;

class DataAktifitasResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-c-users';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Data Kegiatan';
    protected static ?string $label = 'Data Kegiatan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nama kegiatan'),
                TextInput::make('target')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->label('Jumlah target'),
                Select::make('category_id')
                    ->required()
                    ->label('Kategori Warga')
                    ->columnSpan(2)
                    ->relationship('category', 'name')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama kegiatan'),
                TextColumn::make('target')
                    ->label('Jumlah target'),
                TextColumn::make('category.name')
                    ->label('Kategori warga'),
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
            'index' => Pages\ListDataAktifitas::route('/'),
            // 'create' => Pages\CreateDataAktifitas::route('/create'),
            // 'edit' => Pages\EditDataAktifitas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->whereHas('users', function ($query) use ($user) {
            $query->where('users.id', $user?->id);
        });
    }
}
