<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Laravel\Http;

use Drewlabs\Http\Factory\AuthorizationErrorResponseFactoryInterface;
use Drewlabs\Http\Factory\BadRequestResponseFactoryInterface;
use Drewlabs\Http\Factory\OkResponseFactoryInterface;
use Drewlabs\Http\Factory\ResponseFactoryInterface;
use Drewlabs\Http\Factory\ServerErrorResponseFactoryInterface;
use Drewlabs\Http\ResponseHandler as HttpResponseHandler;
use Drewlabs\Laravel\Http\Contracts\ResponseHandler;
use Drewlabs\Laravel\Http\Factory\AuthorizationErrorResponseFactory;
use Drewlabs\Laravel\Http\Factory\BadRequestResponseFactory;
use Drewlabs\Laravel\Http\Factory\LaravelResponseFactory;
use Drewlabs\Laravel\Http\Factory\OkResponseFactory;
use Drewlabs\Laravel\Http\Factory\ServerErrorResponseFactory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\JsonResponse;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;

class JsonApiProvider
{
    /**
     * Register providers for response factory in JSON based HTTP services.
     *
     * @return void
     */
    public static function provide(Container $app)
    {
        $jsonFactory = static function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        };
        $app->bind(AuthorizationErrorResponseFactoryInterface::class, static function () use ($jsonFactory) {
            return new AuthorizationErrorResponseFactory($jsonFactory);
        });

        $app->bind(BadRequestResponseFactoryInterface::class, static function () use ($jsonFactory) {
            return new BadRequestResponseFactory($jsonFactory);
        });

        $app->bind(OkResponseFactoryInterface::class, static function () use ($jsonFactory) {
            return new OkResponseFactory($jsonFactory);
        });

        $app->bind(ServerErrorResponseFactoryInterface::class, static function () use ($jsonFactory) {
            return new ServerErrorResponseFactory($jsonFactory);
        });

        $app->bind(ResponseFactoryInterface::class, static function () use ($jsonFactory) {
            return new LaravelResponseFactory($jsonFactory);
        });

        // Register JSON response handler
        $app->bind(ResponseHandler::class, JsonResponseHandler::class);
        $app->bind(HttpResponseHandler::class, JsonResponseHandler::class);
    }
}
