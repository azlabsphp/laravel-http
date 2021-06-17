<?php

use Illuminate\Container\Container;
use Psr\Container\ContainerInterface;


if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string|null  $make
     * @param  array  $parameters
     * @return mixed|ContainerInterface
     */
    function app($make = null, array $parameters = [])
    {
        if (is_null($make)) {
            return Container::getInstance();
        }
        return Container::getInstance()->make($make, $parameters);
    }
}

if (! function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return \app()->get('config');
        }

        if (is_array($key)) {
            return \app()->get('config')->set($key);
        }

        return \app()->get('config')->get($key, $default);
    }
}

if (!function_exists('drewlabs_http_handlers_configs')) {
    /**
     * Get configuration values from the drewlabs_http_handlers.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_http_handlers_configs($key, $default = null)
    {
        $key = 'drewlabs_http_handlers.' . $key;
        return \config($key, $default);
    }
}

if (!function_exists('is_lumen')) {
    /**
     * Return the default value of the given value.
     *
     * @param  \stdClass  $value
     * @return mixed
     */
    function is_lumen($callback)
    {
        return (get_class($callback) === "Laravel\Lumen\Application") && preg_match('/(5\.[5-8]\..*)|(6\..*)|(7\..*)|(8\..*)/', $callback->version());
    }
}

if (!function_exists('drewlabs_create_ps7_request'))
{
    /**
     * Creates a psr7 server request from php globals or from a symfony request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Psr\Http\Message\ServerRequestInterface
     * @deprecated v3.1
     */
    function drewlabs_create_ps7_request(\Symfony\Component\HttpFoundation\Request $request = null)
    {
        return drewlabs_create_psr7_request($request);
    }
}

if (!function_exists('drewlabs_create_psr7_request'))
{
    /**
     * Creates a psr7 server request from php globals or from a symfony request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    function drewlabs_create_psr7_request(\Symfony\Component\HttpFoundation\Request $request = null)
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        if ($request) {
            $psrHttpFactory = new \Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
            return $psrHttpFactory->createRequest($request);
        }
        $psrHttpFactory = new \Nyholm\Psr7Server\ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        );
        return $psrHttpFactory->fromGlobals();
    }
}