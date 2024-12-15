<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Products\Resources\ProductResource;
use App\Filament\Resources\EstimateResource\Pages;
use App\Filament\Resources\EstimateResource\RelationManagers;
use App\Models\Shop\Product;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
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

class EstimateResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required(),
                Forms\Components\Toggle::make('use_name_as_title')
                    ->label('usar o Nome como título')
                    ->required(),
                Forms\Components\TextInput::make('currency_symbol')
                    ->label('Moeda')
                    ->required(),
                Forms\Components\TextInput::make('duration_rate')
                    ->label('Valor unitário')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Section::make('Seções')
                    ->headerActions([
                        Action::make('Adiciona seção somente texto')
                            ->color('primary')
                            ->action(fn(Forms\Set $set) => $set('items', [])),
                        Action::make('Adiciona seção de precificação')
                            ->color('info')
                            ->action(fn(Forms\Set $set) => $set('items', [])),
                    ])
                    ->schema([
                        static::getItemsRepeater(),
                    ]),
            ]);
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('sections')
            ->relationship()
            ->schema([
                Forms\Components\RichEditor::make('text')
                    ->label('Texto')
                    ->columnSpanFull(),
                Forms\Components\Radio::make('type')
                    ->label('Tipo')
                    ->options([
                        'text' => 'Texto',
                        'prices' => 'Precificar',
                    ])
                    ->default('text')
                    ->inline()
                    ->live(),
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Toggle::make('obligatory')
                            ->inline(false)
                            ->default(true)
                            ->label('Obrigatório'),
                        Forms\Components\TextInput::make('description')
                            ->label('descrição')
                            ->columnSpan([
                                'lg' => 3,
                                'xl' => 4
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('duration')
                            ->label('Duração')
                            ->required()
                            ->numeric()
                            ->live()
                            ->default(0),
                        Forms\Components\TextInput::make('duration_rate')
                            ->label('Valor')
                            ->required()
                            ->numeric()
                            ->live()
                            ->default(0),
                        Forms\Components\TextInput::make('price')
                            ->label('Total')
                            ->required()
                            ->default(0),
                    ])
                    ->columns([
                        'lg' => 6,
                        'xl' => 8
                    ])
                    ->orderColumn('position')
                    ->reorderableWithButtons()
                    ->hidden(fn($record): bool => $record->type !== 'prices')
            ])
            ->orderColumn('position')
            ->reorderableWithButtons()
            ->cloneable()
            ->hiddenLabel();
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
                        TextColumn::make('currency_symbol')
                            ->label(__('app.estimate.currency_symbol'))
                            ->grow(false)
                            ->color('gray'),
                        TextColumn::make('duration_rate')
                            ->label(__('app.estimate.duration_rate'))
                            ->weight(FontWeight::Bold)
                            ->money(fn($record): string => $record->currency_symbol)
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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
