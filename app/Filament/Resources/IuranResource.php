<?php

namespace App\Filament\Resources;

use Filament\Panel;
use Filament\Forms;
use Filament\Tables;
use App\Models\Iuran;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subscription;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\IuranResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\IuranResource\RelationManagers;
use Illuminate\Support\Facades\Auth;

class IuranResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Kategori';
    protected static ?string $navigationLabel = 'Iuran';
    protected static ?string $label = 'Kategori Iuran';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->columnSpan(2)
                    ->label('Nama iuran'),
                TextInput::make('amount')
                    ->required()
                    ->prefix('Rp.')
                    ->label('Jumlah nominal')
                    ->mask('999.999.999')
                    ->dehydrateStateUsing(fn ($state) => preg_replace('/[^0-9]/', '', (string) $state)),
                Select::make('category_id')
                    ->relationship('categories', 'name')
                    ->required()
                    ->label('Kategori'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama iuran')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah nominal (Rp)')
                    ->money('IDR')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable(),
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
            'index' => Pages\ListIurans::route('/'),
            // 'create' => Pages\CreateIuran::route('/create'),
            // 'edit' => Pages\EditIuran::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user?->isSuperAdmin() ?? false;
    }
}
