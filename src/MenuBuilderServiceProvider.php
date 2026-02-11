<?php

namespace Vendor\FilamentMenuBuilder;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Vendor\FilamentMenuBuilder\Resources\MenuResource;

class MenuBuilderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filament-menu-builder.php', 'filament-menu-builder');
        
        $this->app->bind('menu-builder', function () {
            return new MenuBuilder();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/filament-menu-builder.php' => config_path('filament-menu-builder.php'),
        ], 'filament-menu-builder-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'filament-menu-builder-migrations');

        FilamentAsset::register([
            Css::make('filament-menu-builder-css', __DIR__.'/../resources/dist/filament-menu-builder.css'),
        ]);
    }
}
