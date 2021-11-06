<?php

namespace Drewlabs\Packages\Http;

use Illuminate\Contracts\Container\Container;
/**
 * @deprecated v1.0.3 The bindinds are provided in the ServiceProvider
 * Note: Will be remove in version 2.0.x
 * @package Drewlabs\Packages\Http
 */
class HttpUtils
{
    public static function routes(
        Container $app,
        $route_prefix = null
    ) {
        $app->router->group(
            [
                'namespace' => 'Drewlabs\\Packages\\Http\\Controllers',
                'prefix' => $route_prefix
            ],
            function ($router) {
                $router->get('unique', 'TableColumnUniqueRuleController');
            }
        );
    }
}
