<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Cors\ConfigurationBuilder;
use Drewlabs\Cors\Cors;
use Drewlabs\Cors\CorsInterface;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Drewlabs\Packages\Http\Contracts\ResponseHandler;
use Drewlabs\Packages\Http\Factory\LaravelRequestFactory;
use Drewlabs\Packages\Http\Guards\GuessGuard;
use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;
use Drewlabs\Http\ResponseHandler as HttpResponseHandler;
use Drewlabs\Packages\Http\Factory\ViewResponseFactory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/config' => $this->app->basePath('config')], 'drewlabs-http');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register cors interface
        $this->app->bind(CorsInterface::class, function ($app) {
            $config = $app['config'];
            return new Cors(ConfigurationManager::getInstance()->get('cors', $config->get('http.cors')) ?? ConfigurationBuilder::new()->toArray());
        });

        // Register request factory interface
        $this->app->bind(RequestFactoryInterface::class, function() {
            return new LaravelRequestFactory();
        });

        // Register view response factory
        $this->app->bind(ViewResponseFactoryInterface::class, function() {
            return new ViewResponseFactory();
        });

        // Register an anonymous guard, that allow to run application without 
        // worrying about any undefined application guard issues
        $this->registerGuessGuard();

        // Register response handlers types
        $this->registerResponseHandlers();
    }

    private function registerGuessGuard()
    {
        Auth::resolved(function ($auth) {
            $auth->extend('anonymous', function ($app, $name, array $config) use ($auth) {
                return tap($this->createGuessGuard($auth, $config), function ($guard) use ($app) {
                    $app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    private function registerResponseHandlers()
    {
        if (!$this->app->bound(ResponseHandler::class)) {
            $this->app->bind(ResponseHandler::class, JsonResponse::class);
        }
        if (!$this->app->bound(HttpResponseHandler::class)) {
            $this->app->bind(HttpResponseHandler::class, JsonResponse::class);
        }
    }

    /**
     * Register guess the guard.
     *
     * @param \Illuminate\Contracts\Auth\Factory  $auth
     * @param array $config
     * @return RequestGuard
     */
    private function createGuessGuard($auth, $config)
    {
        return new RequestGuard(new GuessGuard(), $this->app->make('request'), null);
    }
}
