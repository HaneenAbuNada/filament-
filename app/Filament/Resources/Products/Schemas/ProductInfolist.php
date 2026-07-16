<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
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
}
