<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        MarkdownEditor::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Pricing and stock')
                    ->schema([
                        TextInput::make('price')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->required(),
                        TextInput::make('stock')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Media and status')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('products'),
                        Toggle::make('is_active')
                            ->default(true),
                        Toggle::make('is_featured')
                            ->default(false),
                    ])
                    ->columns(3),
            ]);
    }
}
