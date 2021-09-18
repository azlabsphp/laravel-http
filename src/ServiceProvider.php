<?php

namespace Drewlabs\Packages\Http;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Drewlabs\Contracts\Validator\Validator as ValidatorContract;
use Drewlabs\Core\Validator\Contracts\IValidator as Validator;
use Drewlabs\Core\Validator\InputsValidator;
use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Drewlabs\Packages\Http\Contracts\IDataProviderControllerActionHandler;
use Drewlabs\Packages\Http\Controllers\ApiDataProviderController;
use Drewlabs\Packages\Http\Middleware\Cors\Contracts\CorsServicesInterface;
use Drewlabs\Packages\Http\Middleware\Cors\CorsServices;

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
        $this->app->bind(ValidatorContract::class, function ($app) {
            return new InputsValidator($app['validator']);
        });
        $this->app->when(ApiDataProviderController::class)
            ->needs(IDataProviderControllerActionHandler::class)
            ->give(function () {
                return new DataProviderControllerActionHandler();
            });
        $this->app->when(ApiDataProviderController::class)
            ->needs(IActionResponseHandler::class)
            ->give(function () {
                return new ActionResponseHandler();
            });
    }
}
