<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;

class SettingsPage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $title = 'Settings';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    protected static string $view = 'filament.pages.settings';

    protected $currency = null;

    public function mount(): void
    {
        $settings = Setting::firstOrCreate(['user_id' => Auth::id()]);

        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(Auth::id()),

                        Grid::make([
                            'sm' => 1,
                            'lg' => 2,
                            'xl' => 3,
                        ])
                            ->schema([
                                Forms\Components\Select::make('currency')
                                    ->label('Currency')
                                    ->options(function () {
                                        $keys = array_keys(config('money.currencies'));
                                        return array_combine($keys, $keys);
                                    })
                                    ->default('BRL')
                                    ->searchable()
                                    ->required(),
                                Forms\Components\TextInput::make('hourly_rate')
                                    ->label('Hourly Rate')
                                    ->extraInputAttributes(['onfocus' => 'this.select()'])
                                    ->numeric()
                                    ->suffix('/hour')
                                    ->default(0)
                        ]),
                    ])
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $data['user_id'] = Auth::id();

        Setting::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        dump($data);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
