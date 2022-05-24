<?php

namespace Drewlabs\Packages\Http\Facades;

use BadMethodCallException;
use Drewlabs\Packages\Http\ServerRequest;

/**
 * @method static string path(mixed $request)
 * @method static boolean hasHeader(mixed $request, string $header)
 * @method static string getHeader(mixed $request, string $header, string $default = null)
 * @method static string getMethod(mixed $request)
 * @method static bool isMethod(mixed $request, string $method)
 * @method static bool isSupported($request)
 * @method static array|string server(string $key = null)
 * @method static array ips()
 * @method static string ip()
 * 
 * @package Drewlabs\Packages\Http\Facades
 */
class HttpRequest
{
    public static function __callStatic($name, $arguments)
    {
        if (empty($arguments)) {
            throw new BadMethodCallException(HttpRequest::class . ' is facade to psr7, symfony, etc... request types, therefor calling method statically requires a least first parameter to be a supported request type');
        }
        return (new ServerRequest($arguments[0]))->{$name}(...array_slice($arguments, 1));
    }
}
