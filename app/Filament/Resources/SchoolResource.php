<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolResource\Pages;
use App\Models\Enviroment;
use App\Models\Feature;
use App\Models\School;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        $featuresToggles = Feature::all()
            ->map(fn (Feature $feature) => Toggle::make(name: $feature->name)->default(false))->toArray();
        $enviroments = Enviroment::all()->map(function (Enviroment $enviroment) {
            return FieldSet::make($enviroment->name)
                ->statePath($enviroment->name)
                ->columnSpanFull()
                ->schema([TextInput::make('value'), TextInput::make('other')]);
        }
        )->toArray();

        return $form
            ->schema([
                Tabs::make('Tabs')->tabs([
                    Tab::make('Main')->schema([
                        Group::make([
                            TextInput::make('id')->label('School ID')->disabledOn('edit')->unique(ignoreRecord: true)
                                ->required()
                                ->debounce(800)
                                ->live()->afterStateUpdated(function ($state, Set $set) {
                                    $set('dbname', $state);
                                    $set('dbuser', $state);

                                }),
                            TextInput::make('name')->label('Name')->required(),
                        ])->columns(2),
                        Fieldset::make('Database information')
                            ->schema([
                                TextInput::make('dbname')->label('Database name (auto filled)')->prefix(env('TENANT_DB_PREFIX'))->required(),
                                TextInput::make('dbuser')->label('Database user (auto filled)')->prefix(env('TENANT_DB_PREFIX'))->required(),
                                TextInput::make('dbpassword')->label('Database password (auto filled)')->default(env('TENANT_DB_PASSWORD'))->required(),

                            ])->columns(3),
                    ]),
                    Tab::make('Enviroments')->schema([
                        Group::make()->statePath('enviroments')
                            ->schema($enviroments)
                            ->columns(['sm' => 2, 'md' => 6])->grow(false),
                        // Repeater::make('enviroments')
                        //     ->label(null)
                        //     ->schema([
                        //         TextInput::make('key')
                        //             ->readOnly()
                        //             ->distinct()
                        //             ->label('Key')
                        //             ->required(),
                        //         TextInput::make('value')
                        //             ->label('Value')->nullable(),
                        //         TextInput::make('other')
                        //             ->label('Other value')->nullable(),

                        //     ])
                        //     ->columnSpanFull()
                        //     ->columns(3)
                        //     ->addable(false)
                        //     ->deletable(false)
                        //     ->reorderable(false)
                        //     ->collapsible()
                        //     ->afterStateHydrated(function (Repeater $component, $state) {
                        //         $configs = new Collection($state);
                        //         $array = Enviroment::all()->pluck('name')->map(function ($key) use ($configs) {
                        //             $config = $configs->firstWhere('key', $key);

                        //             return ['key' => $key, 'value' => $config['value'] ?? null, 'other' => $config['other'] ?? null];
                        //         })->toArray();
                        //         $component->state($array);

                        //     })->itemLabel(fn (array $state): ?string => $state['key'] ?? null),
                    ]),
                    Tab::make('Features')->schema([
                        Group::make()->statePath('features')
                            ->schema($featuresToggles)
                            ->columns(['sm' => 2, 'md' => 6])->grow(false),
                    ]),
                ])->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}
