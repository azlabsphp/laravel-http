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

use Drewlabs\Laravel\Http\Response;

/**
 * @method static Response setHeader($response, string $key, $value)
 * @method static boolean  hasHeader(mixed $response, string $header)
 * @method static string   getHeader(mixed $response, string $header, string $default = null)
 * @method static bool     isSupported($response)
 */
class HttpResponse
{
    public static function __callStatic($name, $arguments)
    {
        if (empty($arguments)) {
            throw new \BadMethodCallException(static::class.' is facade to psr7, symfony, etc... response types, therefor calling method statically requires a least first parameter to be a supported request type');
        }

        return (new Response($arguments[0]))->{$name}(...\array_slice($arguments, 1));
    }
}
