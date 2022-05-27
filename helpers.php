<?php

use Drewlabs\Packages\Http\ConfigurationManager;
use Drewlabs\Packages\Http\ServerRequest;
use Psr\Container\ContainerInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Nyholm\Psr7Server\ServerRequestCreator;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string|null  $abstract
     * @param  array  $parameters
     * @return mixed|ContainerInterface
     */
    function app($abstract = null, array $parameters = [])
    {
        if (null === $abstract) {
            return \Illuminate\Container\Container::getInstance();
        }
        return \Illuminate\Container\Container::getInstance()->make($abstract, $parameters);
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
        return ConfigurationManager::getInstance()->get($key, $default);
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
        return (get_class($callback) === "Laravel\Lumen\Application") && preg_match('/(5\.[5-8]\..*)|(6\..*)|(7\..*)|(8\..*)|(9\..*)/', $callback->version());
    }
}

if (!function_exists('drewlabs_create_ps7_request')) {
    /**
     * Creates a psr7 server request from php globals or from a symfony request
     *
     * @param Request $request
     * @return \Psr\Http\Message\ServerRequestInterface
     * @deprecated v2.3.x
     */
    function drewlabs_create_ps7_request(Request $request = null)
    {
        return drewlabs_create_psr7_request($request);
    }
}

if (!function_exists('drewlabs_create_psr7_request')) {
    /**
     * Creates a psr7 server request from php globals or from a symfony request
     *
     * @param Request $request
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    function drewlabs_create_psr7_request(Request $request = null)
    {
        $psr17Factory = new Psr17Factory();
        if ($request) {
            $psrHttpFactory = new PsrHttpFactory(
                $psr17Factory,
                $psr17Factory,
                $psr17Factory,
                $psr17Factory
            );
            return $psrHttpFactory->createRequest($request);
        }
        $psrHttpFactory = new ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        );
        return $psrHttpFactory->fromGlobals();
    }
}

if (!function_exists('get_illuminate_request_ip')) {
    /**
     * Returns the IP address of the client request
     * 
     * @param mixed $request 
     * @return string|null 
     */
    function get_illuminate_request_ip($request)
    {
        return (new ServerRequest($request))->ip();
    }
}
