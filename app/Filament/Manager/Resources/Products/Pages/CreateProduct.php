<?php

namespace App\Filament\Manager\Resources\Products\Pages;

use App\Filament\Manager\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
