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
    public static function provideJsonFactories(Container $app)
    {
        $app->bind(AuthorizationErrorResponseFactoryInterface::class, function () {
            return new AuthorizationErrorResponseFactory(function ($data = null, $status = 200, $headers = []) {
                return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            });
        });

        $app->bind(BadRequestResponseFactoryInterface::class, function () {
            return new BadRequestResponseFactory(function ($data = null, $status = 200, $headers = []) {
                return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            });
        });

        $app->bind(OkResponseFactoryInterface::class, function () {
            return new OkResponseFactory(function ($data = null, $status = 200, $headers = []) {
                return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            });
        });

        $app->bind(ServerErrorResponseFactoryInterface::class, function () {
            return new ServerErrorResponseFactory(function ($data = null, $status = 200, $headers = []) {
                return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            });
        });

        $app->bind(ResponseFactoryInterface::class, function () {
            return new LaravelResponseFactory(function ($data = null, $status = 200, $headers = []) {
                return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            });
        });

        $app->bind(ResponseHandler::class, function ($app) {
            return $this->createJsonResponseHandler($app);
        });

        $app->bind(HttpResponseHandler::class, function ($app) {
            return $this->createJsonResponseHandler($app);
        });
    }


    private function createJsonResponseHandler($app)
    {
        return new JsonResponse($app->environment('debug'));
    }
}
