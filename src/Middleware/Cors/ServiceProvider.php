<?php

namespace Drewlabs\Packages\Http\Middleware\Cors;

use Drewlabs\Packages\Http\ConfigurationManager;
use Drewlabs\Packages\Http\Middleware\Cors\Contracts\CorsServicesInterface;
use Drewlabs\Packages\Http\Middleware\Cors\CorsServices;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/' => $this->app->basePath('config'),
        ], 'drewlabs-cors');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CorsServicesInterface::class, function () {
            return new CorsServices(ConfigurationManager::getInstance()->get('cors', null));
        });
    }
}
