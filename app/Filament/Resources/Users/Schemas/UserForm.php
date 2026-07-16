<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;  
use Filament\Forms\Components\TextInput; 
use Filament\Forms\Components\Select;     
use App\Models\Country;                   
use App\Models\State;                   

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->email()
                    ->required(),

                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create'),

                Section::make('Location')
                    ->schema([
                        Select::make('country_id')
                            ->label('Country')
                            ->options(Country::pluck('name', 'id'))
                            ->reactive()  
                            ->afterStateUpdated(function (callable $set) {
                                $set('state_id', null);  
                                $set('city_id', null);   
                            }),
                            
 
                        Select::make('state_id')
                            ->label('State')
                            ->reactive()  
                            ->options(function (callable $get) {
                                $countryId = $get('country_id');  
                                if (!$countryId) {
                                    return [];
                                }
                                return State::where('country_id', $countryId)->pluck('name', 'id');
                            })
                            ->afterStateUpdated(function (callable $set) {
                                $set('city_id', null);  
                            }),
                            
                        Select::make('city_id')
                            ->label('City')
                            ->options(function (callable $get) {
                                $stateId = $get('state_id');  
                                if (!$stateId) {
                                    return [];
                                }
                                return City::where('state_id', $stateId)->pluck('name', 'id');
                            }),
                    ]),
            ]);
    }
}