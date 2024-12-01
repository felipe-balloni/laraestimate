<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstimateResource\Pages;
use App\Filament\Resources\EstimateResource\RelationManagers;
use Filament\Forms;
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
                    ->required(),
                Forms\Components\Toggle::make('use_name_as_title')
                    ->required(),
                Forms\Components\TextInput::make('currency_symbol'),
                Forms\Components\TextInput::make('duration_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
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
