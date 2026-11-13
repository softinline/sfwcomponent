<?php

namespace Softinline\SfwComponent;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class SfwComponentServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/views', 'sfwcomponent');
        $this->loadJsonTranslationsFrom(__DIR__.'/resources/lang');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'sfw-migrations');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/softinline/sfwcomponent'),
        ], 'sfw-views');

        $this->publishes([
            __DIR__.'/resources' => public_path('vendor/softinline/sfwcomponent'),
        ], 'sfw-assets');

        $router->middlewareGroup('SfwProtected', [SfwProtectedMiddleware::class]);
    }

    public function register()
    {
        $this->app->make('Softinline\SfwComponent\SfwConfig');
        $this->app->make('Softinline\SfwComponent\SfwComponent');
    }
}
