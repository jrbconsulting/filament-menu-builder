<?php

namespace Vendor\FilamentMenuBuilder\Resources\MenuResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use YourVendor\FilamentMenuBuilder\Resources\MenuResource;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}