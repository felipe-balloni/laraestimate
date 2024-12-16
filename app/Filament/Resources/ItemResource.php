<?php

namespace App\Filament\Resources;

use Akaunting\Money\Money;
use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function getForm(): array
    {
        return [
            Forms\Components\Toggle::make('obligatory')
                ->inline(false)
                ->default(true)
                ->label('Obrigatório'),
            Forms\Components\TextInput::make('description')
                ->label('Descrição')
                ->columnSpan([
                    'xl' => 3,
                    'lg' => 5,
                    'md' => 3,
                ])
                ->placeholder('Feature X')
                ->required(),
            Forms\Components\TextInput::make('duration')
                ->label('Duração')
                ->extraInputAttributes(['onfocus' => 'this.select()'])
                ->columnSpan([
                    'xl' => 1,
                    'lg' => 2,
                    'md' => 3,
                ])
                ->placeholder('15 dias')
                ->required(),
            Forms\Components\TextInput::make('duration_rate')
                ->label('Valor')
                ->extraInputAttributes(['onfocus' => 'this.select()'])
                ->columnSpan([
                    'xl' => 1,
                    'lg' => 2,
                    'md' => 3,
                ])
                ->placeholder('R$ 60/h')
                ->required(),
            Forms\Components\TextInput::make('price')
                ->label('Total')
                ->extraInputAttributes(['onfocus' => 'this.select()'])
                ->columnSpan([
                    'xl' => 2,
                    'lg' => 2,
                    'md' => 3,
                ])
                ->numeric()
                ->prefix(currency('BRL')->getSymbol())
                ->currencyMask(
                    currency('BRL')->getThousandsSeparator(),
                    currency('BRL')->getDecimalMark(),
                    currency('BRL')->getPrecision()
                )
                ->maxValue(42949672.95)
                ->required()
                ->default(0),
        ];
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema(self::getForm());
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('obligatory')
                    ->badge(),
            ])
            ->filters([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
