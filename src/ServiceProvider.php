<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Contracts\Http\BinaryResponseHandler;
use Drewlabs\Contracts\Http\UnAuthorizedResponseHandler;
use Drewlabs\Contracts\Http\ViewResponseHandler;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Drewlabs\Contracts\Validator\Validator;
use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Drewlabs\Packages\Http\Contracts\IDataProviderControllerActionHandler;
use Drewlabs\Packages\Http\Controllers\ApiDataProviderController;
use Drewlabs\Packages\Http\Guards\AnonymousGuard;
use Drewlabs\Packages\Http\Middleware\Cors\Contracts\CorsServiceInterface;
use Drewlabs\Packages\Http\Middleware\Cors\CorsService;
use Drewlabs\Packages\Http\ViewResponseHandler as HttpViewResponseHandler;
use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes(
            [
                __DIR__ . '/config' => $this->app->basePath('config'),
            ],
            'drewlabs-http-configs'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(CorsServiceInterface::class, function () {
            return new CorsService(ConfigurationManager::getInstance()->get('cors', null));
        });
        if (class_exists(\Drewlabs\Core\Validator\InputsValidator::class)) {
            // Register ViewModel validator providers
            $this->app->bind(Validator::class, function ($app) {
                return new \Drewlabs\Core\Validator\InputsValidator($app['validator']);
            });
        }
        $this->app->when(ApiDataProviderController::class)
            ->needs(IDataProviderControllerActionHandler::class)
            ->give(function () {
                return new DataProviderControllerActionHandler();
            });

        // Register an anonymous guard, that allow to run application without 
        // worrying about any undefined application guard issues
        $this->registerAnonymousGuard();

        // Register response handlers types
        $this->registerResponseHandlers();

        // By default try to bind the {@see IActionResponseHandler::class} if it has not
        // been bounded by developpers in project root AppServiceProvider class
        if (!$this->app->bound(IActionResponseHandler::class)) {
            $this->app->bind(IActionResponseHandler::class, JsonApiResponseHandler::class);
        }
    }

    private function registerAnonymousGuard()
    {
        Auth::resolved(function ($auth) {
            $auth->extend('anonymous', function ($app, $name, array $config) use ($auth) {
                return tap($this->createAnonymousGuard($auth, $config), function ($guard) use ($app) {
                    $app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }


    private function registerResponseHandlers()
    {
        $this->app->bind(BinaryResponseHandler::class, BinaryFileResponse::class);
        $this->app->bind(UnAuthorizedResponseHandler::class, UnAuthorizedResponse::class);
        $this->app->bind(ViewResponseHandler::class, HttpViewResponseHandler::class);
    }

    /**
     * Register the guard.
     *
     * @param \Illuminate\Contracts\Auth\Factory  $auth
     * @param array $config
     * @return RequestGuard
     */
    private function createAnonymousGuard($auth, $config)
    {
        return new RequestGuard(
            new AnonymousGuard(),
            $this->app->make('request'),
            null
        );
    }
}
