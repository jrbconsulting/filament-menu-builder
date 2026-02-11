<?php

namespace Vendor\FilamentMenuBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getNested(?string $location = null)
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static \Vendor\FilamentMenuBuilder\Models\Menu create(array $attributes)
 */
class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament-menu-builder';
    }
}
