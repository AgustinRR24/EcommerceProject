<?php

namespace App\Filament\Customers\Resources\OrderResource\Pages;

use App\Filament\Customers\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
