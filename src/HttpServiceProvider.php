<?php

namespace Drewlabs\Packages\Http;

use Illuminate\Support\ServiceProvider;
use Drewlabs\Contracts\Validator\Validator as ValidatorContract;
use Drewlabs\Core\Validator\Contracts\IValidator as Validator;
use Drewlabs\Core\Validator\InputsValidator;

class HttpServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/config' => base_path('config'),
        ], 'drewlabs-http-configs');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(\Drewlabs\Packages\Http\Controllers\ApiDataProviderController::class)
            ->needs(\Drewlabs\Packages\Http\Contracts\IDataProviderControllerActionHandler::class)
            ->give(function () {
                return new DataProviderControllerActionHandler();
            });
        // Register ViewModel validator providers
        $this->app->bind(Validator::class, function ($app) {
            return new InputsValidator($app['validator']);
        });
        $this->app->bind(ValidatorContract::class, function ($app) {
            return new InputsValidator($app['validator']);
        });
        $this->app->when(\Drewlabs\Packages\Http\Controllers\ApiDataProviderController::class)
            ->needs(\Drewlabs\Packages\Http\Contracts\IActionResponseHandler::class)
            ->give(function () {
                return new ActionResponseHandler();
            });
    }
}
