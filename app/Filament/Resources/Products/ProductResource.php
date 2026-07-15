<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables\Table;

use Filament\Schemas\Schema; 

use Filament\Forms\Components\Group as FormGroup;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Actions\Action;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Schema $schema): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('public'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('price'),
                TextColumn::make('stock'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(), 
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([  
                Tabs::make('Product Details')
                    ->tabs([
                        Tab::make('Product Info')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                TextEntry::make('id')->label('Product ID')->weight('bold')->color('primary'),
                                TextEntry::make('name')->label('Product Name')->weight('bold'),
                                TextEntry::make('sku')->label('Product SKU')->badge()->color('success'),
                                TextEntry::make('description')->label('Description'),
                                TextEntry::make('created_at')->label('Creation Date')->date('m/d/Y'),
                            ]),

                        Tab::make('Pricing & Stock')
                            ->icon('heroicon-o-currency-dollar')
                            ->badge(10) 
                            ->badgeColor('info')
                            ->schema([
                                TextEntry::make('price')->label('Price')->weight('bold'),
                                TextEntry::make('stock')->label('Stock')->weight('bold'),
                            ]),

                        Tab::make('Media & Status')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                ImageEntry::make('image')->label('Product Image')->disk('public'),
                                IconEntry::make('is_active')->label('Is Active?')->boolean(),
                                IconEntry::make('is_featured')->label('Is Featured?')->boolean(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->vertical(), 
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'), 
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}