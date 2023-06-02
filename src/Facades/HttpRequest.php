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

namespace Drewlabs\Laravel\Http\Facades;

use Drewlabs\Laravel\Http\ServerRequest;

/**
 * @method static string       path(mixed $request)
 * @method static boolean      hasHeader(mixed $request, string $header)
 * @method static string       getHeader(mixed $request, string $header, string $default = null)
 * @method static string       getMethod(mixed $request)
 * @method static bool         isMethod(mixed $request, string $method)
 * @method static bool         isSupported($request)
 * @method static array|string server($request, string $key = null)
 * @method static array        ips($request)
 * @method static string       ip($request)
 * @method static string|array cookie($request, string $name = null)
 * @method static string|array query($request, string $name = null)
 * @method static string|array input($request, string $name = null)
 * @method static string|array all($request, $keys = [])
 */
class HttpRequest
{
    public static function __callStatic($name, $arguments)
    {
        if (empty($arguments)) {
            throw new \BadMethodCallException(static::class.' is facade to psr7, symfony, etc... request types, therefor calling method statically requires a least first parameter to be a supported request type');
        }

        return (new ServerRequest($arguments[0]))->{$name}(...\array_slice($arguments, 1));
    }
}
