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

use Drewlabs\Cors\ConfigurationBuilder;
use Drewlabs\Cors\Cors;
use Drewlabs\Cors\CorsInterface;
use Drewlabs\Http\Factory\RequestFactoryInterface;
use Drewlabs\Http\Factory\ViewResponseFactoryInterface;
use Drewlabs\Laravel\Http\Factory\LaravelRequestFactory;
use Drewlabs\Laravel\Http\Factory\ViewResponseFactory;
use Drewlabs\Laravel\Http\Guards\GuestGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/config' => $this->app->basePath('config')], 'drewlabs-http');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register cors interface
        $this->app->bind(CorsInterface::class, static function ($app) {
            $config = $app['config'];

            return new Cors(ConfigurationManager::getInstance()->get('cors', $config->get('http.cors')) ?? ConfigurationBuilder::new()->toArray());
        });

        // Register request factory interface
        $this->app->bind(RequestFactoryInterface::class, static function () {
            return new LaravelRequestFactory();
        });

        // Register view response factory
        $this->app->bind(ViewResponseFactoryInterface::class, static function () {
            return new ViewResponseFactory();
        });


        Auth::resolved(function ($auth) {
            $auth->extend('anonymous', function ($app) {
                return tap(new GuestGuard, static function ($guard) use ($app) {
                    $app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }
}
