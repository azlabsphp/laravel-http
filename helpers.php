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

use Drewlabs\Laravel\Http\ConfigurationManager;
use Drewlabs\Laravel\Http\Factory\PsrRequestFactory;
use Drewlabs\Laravel\Http\ServerRequest;
use Illuminate\Container\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     *
     * @return mixed|ContainerInterface
     */
    function app($abstract = null, array $parameters = [])
    {
        return null === $abstract ? Container::getInstance() : Container::getInstance()->make($abstract, $parameters);
    }
}

if (!function_exists('drewlabs_http_handlers_configs')) {
    /**
     * Get configuration values from the drewlabs_http_handlers.php configuration file.
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    function drewlabs_http_handlers_configs($key, $default = null)
    {
        return ConfigurationManager::getInstance()->get($key, $default);
    }
}

if (!function_exists('is_lumen')) {
    /**
     * Return the default value of the given value.
     *
     * @return mixed
     */
    function is_lumen($callback)
    {
        return ("Laravel\Lumen\Application" === $callback::class) && preg_match('/(5\.[5-8]\..*)|(6\..*)|(7\..*)|(8\..*)|(9\..*)/', $callback->version());
    }
}

if (!function_exists('drewlabs_create_psr7_request')) {
    /**
     * Creates a psr7 server request from php globals or from a symfony request.
     *
     * @param Request $request
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    function drewlabs_create_psr7_request(Request $request = null)
    {
        $request = $request ?? Request::createFromGlobals();
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrRequestFactory(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

        return $psrHttpFactory->create($request);
    }
}

if (!function_exists('get_illuminate_request_ip')) {
    /**
     * Returns the IP address of the client request.
     *
     * @param mixed $request
     *
     * @return string|null
     */
    function get_illuminate_request_ip($request)
    {
        return (new ServerRequest($request))->ip();
    }
}
