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

namespace Drewlabs\Laravel\Http\Middleware;

use Drewlabs\Http\Exceptions\HttpException;
use Drewlabs\Laravel\Http\ServerRequest;

class TrustedIpAddress
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, \Closure $next, ...$addresses)
    {
        $serverRequest = new ServerRequest($request);
        if (empty(array_intersect(array_merge($serverRequest->ips() ?? [], [$serverRequest->ip()]), $addresses))) {
            throw HttpException::Unauthorized($request);
        }

        return $next($request);
    }
}
