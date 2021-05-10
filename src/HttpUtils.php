<?php

namespace Drewlabs\Packages\Http;

use Illuminate\Contracts\Container\Container;

class HttpUtils
{
    public static function routes(Container $app, $route_prefix = '')
    {
        $app->{'router'}->group([
            'namespace' => 'Drewlabs\\Packages\\Http\\Controllers',
            'prefix' => $route_prefix
        ], function ($router) {
            $router->get('unique', 'IsUniqueValidatorController@get');
        });
    }
}
