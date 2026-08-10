<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Panel;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Civilian;
use Filament\Forms\Form;
use App\Models\DataWarga;
use App\Models\Education;
use App\Models\Liability;
use Filament\Tables\Table;
use App\Models\CivilianJob;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DataWargaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DataWargaResource\RelationManagers;

class DataWargaResource extends Resource
{
    protected static ?string $model = Civilian::class;

    protected static ?string $navigationIcon = 'heroicon-c-users';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Data Warga';
    protected static ?string $label = 'Data Warga';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('category_id')
                    ->required()
                    ->label('Kategori')
                    ->columnSpan(2)
                    ->multiple()
                    // ->disabled()
                    ->relationship('categories', 'name')
                    ->preload(),
                TextInput::make('full_name')
                    ->required()
                    ->label('Nama lengkap'),

                Select::make('gender')
                    ->required()
                    ->label('Jenis kelamin')
                    ->options([
                        0 => "Pria",
                        1 => "Wanita"
                    ]),
                TextInput::make('born_place')
                    ->required()
                    ->label('Tempat lahir'),
                DatePicker::make('born_date')
                    ->required()
                    ->label('Tanggal lahir'),
                TextInput::make('nik')
                    ->required()
                    ->rules([
                        fn(): Closure => function (string $attribute, $value, Closure $fail) {
                            if ((strlen((string)$value) < 16) || (strlen((string)$value) > 16)) {
                                $fail('NIK harus berisi 16 digit angka.');
                            }
                        },
                    ])
                    ->label('NIK'),
                TextInput::make('home_address')
                    ->required()
                    ->label('Alamat'),
                Select::make('married_status')
                    ->required()
                    ->label('Status Pernikahan')
                    ->options([
                        false => "Belum menikah",
                        true => "Sudah menikah"
                    ]),
                TextInput::make('phone_number')
                    ->required()
                    ->tel()
                    ->startsWith([0, 8])
                    ->label('No. HP'),

                Repeater::make('liabilities')
                    ->required()
                    ->label('Tanggungan')
                    ->relationship()
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
                                "Belum menempuh pendidikan" => "Belum menempuh pendidikan",
                                "TK" => "TK",
                                "SD sederajat" => "SD sederajat",
                                "SMP sederajat" => "SMA sederajat",
                                "SMA sederajat" => "SMA sederajat",
                                "D3" => "D3",
                                "D4/S1" => "D4/S1",
                                "S2" => "S2",
                                "S3" => "S3",

                            ])

                    ])->addActionAlignment(Alignment::End),

                Repeater::make('civilian_pivot_jobs')
                    ->required()
                    ->label('Pekerjaan')
                    ->relationship('civilian_pivot_jobs')
                    ->schema([
                        Select::make('civilian_job_id')
                            ->required()
                            ->label('Tempat kerja')
                            ->columnSpan(3)
                            ->options(CivilianJob::pluck('job_place', 'id')->toArray())
                            ->preload(),

                        Select::make('accepted_date')
                            ->required()
                            ->label('Tahun masuk')
                            ->options([
                                "2010" => "2010",
                                "2011" => "2011",
                                "2012" => "2012",
                                "2013" => "2013",
                                "2014" => "2014",
                                "2015" => "2015",
                                "2016" => "2016",
                                "2017" => "2017",
                                "2018" => "2018",
                                "2019" => "2019",
                                "2020" => "2020",
                            ]),

                        Select::make('retirement_date')
                            ->required()
                            ->label('Tahun berhenti')
                            ->options([
                                "2015" => "2015",
                                "2016" => "2016",
                                "2017" => "2017",
                                "2018" => "2018",
                                "2019" => "2019",
                                "2020" => "2020",
                                "2021" => "2021",
                                "2022" => "2022",
                                "2023" => "2023",
                                "2024" => "2024",
                                "Sekarang" => "Sekarang",
                            ]),
                    ])
                    ->addActionAlignment(Alignment::End)
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
                    ->formatStateUsing(function ($state, Civilian $civilian) {
                        return Carbon::parse($civilian->born_date)->age . ' ' . 'tahun';
                    }),
                TextColumn::make('gender')
                    ->label('Jenis kelamin')
                    ->formatStateUsing(function ($state, Civilian $civilian) {
                        if ($civilian->gender == false) {
                            return 'Pria';
                        }
                        return 'Wanita';
                    }),
                TextColumn::make('born_place')
                    ->label('Tempat tanggal lahir')
                    ->formatStateUsing(function ($state, Civilian $civilian) {
                        return $civilian->born_place . ', ' . Carbon::parse($civilian->born_date)->format('j F o');
                    }),
                TextColumn::make('nik')
                    ->label('NIK'),
                TextColumn::make('home_address')
                    ->label('Alamat'),
                TextColumn::make('married_status')
                    ->label('Status Pernikahan')
                    ->formatStateUsing(function ($state, Civilian $civilian) {
                        if ($civilian->married_status == false) {
                            return 'Belum menikah';
                        }
                        return 'Sudah menikah';
                    }),
                TextColumn::make('phone_number')
                    ->label('No. HP'),
                TextColumn::make('civilian_jobs.job_place')
                    ->label('Pekerjaan'),
                TextColumn::make('liabilities.full_name')
                    ->label('Tanggungan'),
                TextColumn::make('categories.name')
                    ->label('Kategori warga'),
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
            'index' => Pages\ListDataWargas::route('/'),
            'create' => Pages\CreateDataWarga::route('/create'),
            'edit' => Pages\EditDataWarga::route('/{record}/edit'),
        ];
    }
}
