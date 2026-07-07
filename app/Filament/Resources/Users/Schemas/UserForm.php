<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;  

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
            ]);
    }
}