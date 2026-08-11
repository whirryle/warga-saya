<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
  protected static ?string $model = User::class;
  protected static ?string $navigationIcon = 'heroicon-o-users';
  protected static ?string $navigationGroup = 'Master Data';
  protected static ?string $navigationLabel = 'Kelola User';
  protected static ?string $label = 'User';
  protected static ?int $navigationSort = 6;
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('name')
          ->required()
          ->maxLength(255)
          ->label('Nama'),

        Forms\Components\TextInput::make('email')
          ->email()
          ->required()
          ->unique(ignoreRecord: true)
          ->maxLength(255)
          ->label('Email'),

        Forms\Components\TextInput::make('password')
          ->password()
          ->dehydrateStateUsing(fn($state) => Hash::make($state))
          ->dehydrated(fn($state) => filled($state))
          ->required(fn(string $context): bool => $context === 'create')
          ->maxLength(255)
          ->label('Password (kosongkan saat edit jika tidak diubah)'),

        Forms\Components\Select::make('role')
          ->options([
            'super_admin' => 'Super Admin',
            'activity_admin' => 'Activity Admin',
            'user' => 'User',
          ])
          ->required()
          ->live()
          ->label('Role'),

        Forms\Components\Select::make('activites')
          ->relationship('activites', 'name')
          ->multiple()
          ->preload()
          ->visible(fn(Get $get) => $get('role') === 'activity_admin')
          ->label('Tugaskan Kegiatan'),
      ]);
  }
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->searchable()
          ->label('Nama'),
        Tables\Columns\TextColumn::make('email')
          ->searchable()
          ->label('Email'),
        Tables\Columns\TextColumn::make('role')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'super_admin' => 'danger',
            'activity_admin' => 'warning',
            'user' => 'success',
            default => 'gray',
          })
          ->label('Role'),
        Tables\Columns\TextColumn::make('activites.name')
          ->badge()
          ->color('info')
          ->label('Kegiatan yang Dikelola'),
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
      'index' => Pages\ListUsers::route('/'),
    ];
  }
  public static function canViewAny(): bool
  {
    return Auth::user()?->isSuperAdmin() ?? false;
  }
}
