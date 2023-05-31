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

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Http\Exceptions\HttpException;

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
        $addresses = null !== $request->headers->get('X-Real-IP') ? Arr::wrap($request->headers->get('X-Real-IP')) : $request->ips();
        if (empty(array_intersect($addresses, $addresses))) {
            throw HttpException::Unauthorized($request);
        }

        return $next($request);
    }
}
