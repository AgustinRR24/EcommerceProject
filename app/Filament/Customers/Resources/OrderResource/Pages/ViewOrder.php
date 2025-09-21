<?php

namespace App\Filament\Customers\Resources\OrderResource\Pages;

use App\Filament\Customers\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions for customers - read only
        ];
    }
}