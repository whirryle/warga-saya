<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Panel;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Civilian;
use Filament\Forms\Form;
use App\Models\DataIuran;
use Filament\Tables\Table;
use App\Models\Subscription;
use Filament\Resources\Resource;
use App\Models\CivilianPivotCategory;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Models\CivilianPivotSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\DataIuranResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DataIuranResource\RelationManagers;
use Illuminate\Support\Facades\Auth;

class DataIuranResource extends Resource
{
    protected static ?string $model = CivilianPivotSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Iuran';
    protected static ?string $navigationLabel = 'Data Iuran';
    protected static ?string $label = 'Data Iuran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('subscription_id')
                    ->required()
                    ->options(Subscription::pluck('name', 'id'))
                    ->label('Nama iuran')
                    // ->required()
                    ->live(debounce: '1ms')
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Set state untuk civilian_id berdasarkan subscription yang dipilih
                        if ($state) {
                            $civilians = Civilian::whereHas('categories', function ($query) use ($state) {
                                $query->where('category_id', Subscription::find($state)->category_id);
                            })->pluck('full_name', 'id');

                            $set('civilian_id', null); // Reset civilian_id
                            $set('civilians', $civilians); // Set opsi civilian
                        }
                    }),

                Select::make('civilian_id')
                    // ->relationship('subscription', 'name')
                    ->label('Nama warga')
                    ->required()
                    ->options(function (callable $get, ?CivilianPivotSubscription $record) {
                        // Ambil data civilian berdasarkan subscription yang dipilih
                        $options = $get('civilians') ?? [];

                        // Saat edit: pastikan warga yang sedang terpilih tetap tampil
                        if ($record && $record->civilian_id) {
                            $options[$record->civilian_id] = $record->civilian->full_name ?? $record->civilian_id;
                        }

                        return $options;
                    })
                    ->disabled(function (callable $get) {
                        // Nonaktifkan select box jika subscription belum dipilih
                        return !$get('subscription_id');
                    })
                    ->rules([
                        function (Forms\Get $get) {
                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                $exists = CivilianPivotSubscription::where('subscription_id', $get('subscription_id'))
                                    ->where('civilian_id', $value)
                                    ->exists();

                                if ($exists) {
                                    $fail('Warga ini sudah terdaftar untuk iuran ini.');
                                }
                            };
                        },
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('civilian.full_name')
                    ->label('Nama warga')
                    ->searchable(),
                TextColumn::make('subscription.name')
                    ->label('Kategori Iuran')
                    ->searchable(),
                TextColumn::make('subscription.amount')
                    ->label('Nominal iuran')
                    ->money('IDR')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListDataIurans::route('/'),
            'create' => Pages\CreateDataIuran::route('/create'),
            'edit' => Pages\EditDataIuran::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // super admin dapat melihat menu ini (Data Iuran)
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->isSuperAdmin() ?? false;
    }
}
