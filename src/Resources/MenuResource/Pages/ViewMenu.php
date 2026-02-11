<?php

namespace Vendor\FilamentMenuBuilder\Resources\MenuResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use YourVendor\FilamentMenuBuilder\Resources\MenuResource;

class ViewMenu extends ViewRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}