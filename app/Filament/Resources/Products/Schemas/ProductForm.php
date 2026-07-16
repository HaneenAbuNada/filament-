<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Group as FormGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Actions\Action;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([  
                \Filament\Forms\Components\Wizard::make([
                    \Filament\Forms\Components\Wizard\Step::make('Product Info')
                        ->schema([
                            FormGroup::make([
                                TextInput::make('name')->required(),
                                TextInput::make('sku')
                                    ->required()
                                    ->unique(ignorable: fn ($record) => $record),
                            ])->columns(2),
                            MarkdownEditor::make('description'),
                        ]),

                    \Filament\Forms\Components\Wizard\Step::make('Pricing & Stock')
                        ->schema([
                            FormGroup::make([
                                TextInput::make('price')->numeric()->required(),
                                TextInput::make('stock')->numeric()->required(),
                            ])->columns(2),
                        ]),

                    \Filament\Forms\Components\Wizard\Step::make('Media & Status')
                        ->schema([
                            FileUpload::make('image')->disk('public')->directory('products'),
                            Checkbox::make('is_active'),
                            Checkbox::make('is_featured'),
                        ]),
                ])
                ->columnSpanFull()
                ->skippable()
                ->submitAction(
                    Action::make('save')
                        ->label('Save Product')
                        ->color('primary')
                        ->submit('save')
                ),
            ]);
    }
}