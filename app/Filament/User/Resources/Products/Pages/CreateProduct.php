<?php

namespace App\Filament\User\Resources\Products\Pages;

use App\Filament\User\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
