<?php

namespace Softinline\SfwComponent;

use Illuminate\Support\ServiceProvider;

class SfwComponentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        // load views
        $this->loadViewsFrom(__DIR__.'/views', 'sfwcomponent');

        // load routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
                
        // publish views        
        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/softinline/sfwcomponent'),
        ]);

        // publish resources
        $this->publishes([
            __DIR__.'/resources' => public_path('vendor/softinline/sfwcomponent'),
        ], 'public');
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->make('Softinline\SfwComponent\SfwConfig');
        $this->app->make('Softinline\SfwComponent\SfwComponent');        
    }
}
