<?php

namespace App\Filament\Resources;

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
                ->label('descrição')
                ->columnSpan([
                    'xl' => 4,
                    'lg' => 5,
                    'md' => 3,
                ])
                ->required(),
            Forms\Components\TextInput::make('duration')
                ->label('Duração')
                ->columnSpan([
                    'xl' => 1,
                    'lg' => 2,
                    'md' => 3,
                ])
                ->required()
                ->numeric()
                ->live()
                ->default(0),
            Forms\Components\TextInput::make('duration_rate')
                ->label('Valor')
                ->columnSpan([
                    'xl' => 1,
                    'lg' => 2,
                    'md' => 3,
                ])
                ->required()
                ->numeric()
                ->live()
                ->default(0),
            Forms\Components\TextInput::make('price')
                ->label('Total')
                ->columnSpan([
                    'xl' => 1,
                    'lg' => 2,
                    'md' => 3,
                ])
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
                Tables\Columns\TextColumn::make('description')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('price')->sortable(),
                Tables\Columns\TextColumn::make('obligatory')->badge(),
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
