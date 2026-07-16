<?php

namespace App\Filament\Manager\Resources\Products\Pages;

use App\Filament\Manager\Resources\Products\ProductResource;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
}
