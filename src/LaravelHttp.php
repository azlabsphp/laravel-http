<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Http\Factory\AuthorizationErrorResponseFactoryInterface;
use Drewlabs\Http\Factory\BadRequestResponseFactoryInterface;
use Drewlabs\Http\Factory\OkResponseFactoryInterface;
use Drewlabs\Http\Factory\ResponseFactoryInterface;
use Drewlabs\Http\Factory\ServerErrorResponseFactoryInterface;
use Drewlabs\Http\ResponseHandler as HttpResponseHandler;
use Drewlabs\Packages\Http\Contracts\ResponseHandler;
use Drewlabs\Packages\Http\Factory\AuthorizationErrorResponseFactory;
use Drewlabs\Packages\Http\Factory\BadRequestResponseFactory;
use Illuminate\Http\JsonResponse;
use Drewlabs\Packages\Http\Factory\LaravelResponseFactory;
use Drewlabs\Packages\Http\Factory\OkResponseFactory;
use Drewlabs\Packages\Http\Factory\ServerErrorResponseFactory;
use Illuminate\Contracts\Container\Container;

class LaravelHttp
{
    /**
     * Register providers for response factory in JSON based HTTP services
     * 
     * @param Container $app
     * 
     * @return void 
     */
    public static function provideJson(Container $app)
    {
        $jsonFactory = function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        };
        $app->bind(AuthorizationErrorResponseFactoryInterface::class, function () use ($jsonFactory) {
            return new AuthorizationErrorResponseFactory($jsonFactory);
        });

        $app->bind(BadRequestResponseFactoryInterface::class, function () use ($jsonFactory) {
            return new BadRequestResponseFactory($jsonFactory);
        });

        $app->bind(OkResponseFactoryInterface::class, function () use ($jsonFactory) {
            return new OkResponseFactory($jsonFactory);
        });

        $app->bind(ServerErrorResponseFactoryInterface::class, function () use ($jsonFactory) {
            return new ServerErrorResponseFactory($jsonFactory);
        });

        $app->bind(ResponseFactoryInterface::class, function () use ($jsonFactory) {
            return new LaravelResponseFactory($jsonFactory);
        });

        // Register JSON response handler
        $app->bind(ResponseHandler::class, JsonResponseHandler::class);
        $app->bind(HttpResponseHandler::class, JsonResponseHandler::class);
    }
}
