<?php

namespace Vendor\FilamentMenuBuilder;

use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use YourVendor\FilamentMenuBuilder\Resources\MenuResource;

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

        $this->publishes([
            __DIR__.'/../resources/views/' => resource_path('views/vendor/filament-menu-builder'),
        ], 'filament-menu-builder-views');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-menu-builder');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->registerResources();
    }

    protected function registerResources(): void
    {
        // Register Filament resource
        app('filament')->register([
            MenuResource::class,
        ]);
    }
}