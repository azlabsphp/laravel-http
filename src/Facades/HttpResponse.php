<?php

namespace Drewlabs\Packages\Http\Facades;

use BadMethodCallException;
use Drewlabs\Packages\Http\Response;

/**
 * @method static Response setHeader($response, string $key, $value)
 * @method static boolean hasHeader(mixed $response, string $header)
 * @method static string getHeader(mixed $response, string $header, string $default = null)
 * @method static bool isSupported($response)
 * 
 * @package Drewlabs\Packages\Http\Facades
 */
class HttpResponse
{

    public static function __callStatic($name, $arguments)
    {
        if (empty($arguments)) {
            throw new BadMethodCallException(static::class . ' is facade to psr7, symfony, etc... response types, therefor calling method statically requires a least first parameter to be a supported request type');
        }
        return (new Response($arguments[0]))->{$name}(...array_slice($arguments, 1));
    }
}