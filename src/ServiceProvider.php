<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Contracts\Http\BinaryResponseHandler;
use Drewlabs\Contracts\Http\UnAuthorizedResponseHandler;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Drewlabs\Contracts\Validator\Validator;
use Drewlabs\Core\Validator\InputsValidator;
use Drewlabs\Packages\Http\Contracts\IDataProviderControllerActionHandler;
use Drewlabs\Packages\Http\Controllers\ApiDataProviderController;
use Drewlabs\Packages\Http\Guards\AnonymousGuard;
use Drewlabs\Packages\Http\Middleware\Cors\Contracts\CorsServicesInterface;
use Drewlabs\Packages\Http\Middleware\Cors\CorsServices;
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

        $this->app->bind(CorsServicesInterface::class, function () {
            return new CorsServices(ConfigurationManager::getInstance()->get('cors', null));
        });
        // Register ViewModel validator providers
        $this->app->bind(Validator::class, function ($app) {
            return new InputsValidator($app['validator']);
        });
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

        // Routes bindings
        $this->forRoutes($this->app, drewlabs_http_handlers_configs('route_prefix', 'api/v2'));
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

    private function forRoutes($app, $prefix = null)
    {
        if ($router = $app['router'] ?? null) {
            $router->group(
                [
                    'namespace' => 'Drewlabs\\Packages\\Http\\Controllers',
                    'prefix' => $prefix
                ],
                function ($router) {
                    $router->get('unique', 'TableColumnUniqueRuleController');
                }
            );
        }
    }
}
