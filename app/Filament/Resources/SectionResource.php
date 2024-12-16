<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionResource\Pages;
use App\Filament\Resources\SectionResource\RelationManagers;
use App\Models\Estimate;
use App\Models\Item;
use App\Models\Section;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function getForm(): array
    {
        return [
            Forms\Components\Select::make('type')
                ->options([
                    'text' => 'Text',
                    'contact' => 'Contact',
                    'prices' => 'Prices',
                ])
                ->required()
                ->label('Section Type'),

            Forms\Components\Textarea::make('text')
                ->label('Text')
                ->visible(fn ($get) => $get('type') === 'text'),

            Forms\Components\Repeater::make('items')
                ->relationship('items')
                ->schema(ItemResource::getForm())
                // ->columns(2)
                ->columns([
                    'lg' => 6,
                    'xl' => 8
                ])
                ->collapsible()
                ->addActionLabel('Add Item'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('estimate_id')
                    ->label('Estimate')
                    ->options(Estimate::where('user_id', auth()->id())->pluck('name', 'id'))
                    ->required()
                    ->disabledOn('edit'),
                ...self::getForm()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('text')->limit(50),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }
}
