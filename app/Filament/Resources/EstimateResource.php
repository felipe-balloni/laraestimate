<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Products\Resources\ProductResource;
use App\Filament\Resources\EstimateResource\Pages;
use App\Filament\Resources\EstimateResource\RelationManagers;
use App\Models\Section;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EstimateResource extends Resource
{
    public static function form(Form $form): Form
    {
        $userSettings = Setting::where('user_id', Auth::id())->first() ?? new Setting();

        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Section::make('Title')
                        ->columnSpan(2)
                        ->collapsible()
                        ->collapsed(fn (Get $get) => !empty($get('name')))
                        ->heading(fn (Get $get) => $get('name') ?? 'Título')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nome')
                                ->required(),
                            Forms\Components\Toggle::make('use_name_as_title')
                                ->label('Usar o Nome como Título')
                                ->required(),
                        ]),

                    Forms\Components\Section::make('Project Currency')
                        ->columnSpan(1)
                        ->collapsible()
                        ->collapsed(fn (Get $get) => !empty($get('name')))
                        ->schema([
                            Forms\Components\Select::make('currency')
                                ->label('Moeda')
                                ->options(function () {
                                    $keys = array_keys(config('money.currencies') ?? ['BRL']);
                                    return array_combine($keys, $keys);
                                })
                                ->default($userSettings->currency ?? 'BRL')
                                ->extraInputAttributes(['onfocus' => 'this.select()'])
                                ->searchable(),
                            Forms\Components\TextInput::make('hourly_rate')
                                ->label('Hourly Rate')
                                ->default($userSettings->hourly_rate ?? 0)
                                ->extraInputAttributes(['onfocus' => 'this.select()'])
                                ->numeric()
                                ->suffix('/hour'),
                        ]),
                ]),

                Forms\Components\Section::make('Seções')
                    ->headerActions([
                        Action::make('addTextSection')
                            ->label('Adicionar Seção de Texto')
                            ->color('primary')
                            ->action(function (callable $set, $get) {
                                $sections = $get('sections') ?? [];
                                $sections[] = [
                                    'type' => Section::TYPE_TEXT,
                                    'text' => '',
                                ];
                                $set('sections', $sections);
                            })
                            ->hidden(fn ($livewire) => $livewire instanceof Pages\ViewEstimate),

                        Action::make('addPrecificationSection')
                            ->label('Adicionar Seção de Precificação')
                            ->color('secondary')
                            ->action(function (callable $set, $get) {
                                $sections = $get('sections') ?? [];
                                $sections[] = [
                                    'type' => Section::TYPE_PRICES,
                                    'text' => '',
                                ];
                                $set('sections', $sections);
                            })
                            ->hidden(fn ($livewire) => $livewire instanceof Pages\ViewEstimate),
                    ])
                    ->schema([
                        Forms\Components\Repeater::make('sections')
                            ->hiddenLabel()
                            ->relationship()
                            ->addable(false)
                            ->reorderable()
                            ->orderColumn('order')
                            ->reorderableWithDragAndDrop()
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Forms\Components\TextInput::make('estimate_id')
                                    ->required()
                                    ->hidden(),

                                Forms\Components\Hidden::make('type')
                                    ->required()
                                    ->dehydrated(true)
                                    ->live(),

                                Forms\Components\RichEditor::make('text')
                                    ->label('Texto da Seção'),

                                Forms\Components\Repeater::make('items')
                                    ->relationship()
                                    ->reorderable()
                                    ->orderColumn('order')
                                    ->reorderableWithDragAndDrop()
                                    ->schema(ItemResource::getForm())
                                    ->columns(2)
                                    ->collapsible()
                                    ->addActionLabel('Add Item')
                                    ->visible(fn ($get) => $get('type') === Section::TYPE_PRICES)
                                    ->itemLabel(fn (array $state): ?string => $state['description'])
                                    ->live()
                                    ->columns([
                                        'xl' => 8,
                                        'lg' => 6,
                                        'md' => 3,
                                    ])
                                    ->defaultItems(0)
                                    ->minItems(1)
                            ])
                            ->afterStateHydrated(function (array &$state, $get) {
                                foreach ($state as &$section) {
                                    $section['estimate_id'] = $get('id');
                                }
                            })
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => $data)
                            ->itemLabel(function (array $state): ?string {
                                $label = $state['type'] === Section::TYPE_PRICES
                                    ? 'Seção de Precificação'
                                    : 'Seção de Texto';

                                $label .= ': ' . Str::limit($state['text'], 30);
                                return $label;
                            })
                            ->defaultItems(0)
                            ->minItems(0)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->label(__('app.estimate.name'))
                        ->weight(FontWeight::Bold)
                        ->searchable(),
                    TextColumn::make('id')
                        ->label('ID')
                        ->color('gray')
                        ->searchable(),
                    Split::make([
                        TextColumn::make('currency')
                            ->label(__('app.estimate.currency'))
                            ->grow(false)
                            ->color('gray'),
                        TextColumn::make('duration_rate')
                            ->label(__('app.estimate.duration_rate'))
                            ->weight(FontWeight::Bold)
                            // ->money(fn($record): string => $record->currency)
                            ->color('gray'),
                    ]),
                ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([
                12
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstimates::route('/'),
            'create' => Pages\CreateEstimate::route('/create'),
            'view' => Pages\ViewEstimate::route('/{record}'),
            'edit' => Pages\EditEstimate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('app.estimate.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.estimate.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.estimate.plural');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-user-group';
    }
}
