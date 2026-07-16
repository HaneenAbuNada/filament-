<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('state_id')
                ->relationship('state', 'name')
                ->getOptionLabelFromRecordUsing(fn ($record): string => "{$record->country->name} — {$record->name}")
                ->searchable(['name'])
                ->preload()
                ->required(),
            TextInput::make('name')->required()->maxLength(255),
        ])->columns(2);
    }
}
