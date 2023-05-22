<?php

namespace Drewlabs\Packages\Http\Middleware;

use Closure;
use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Http\Exceptions\HttpException;

class TrustedIpAddress
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$addresses)
    {
        $addresses = null !== $request->headers->get('X-Real-IP') ? Arr::wrap($request->headers->get('X-Real-IP')) : $request->ips();
        if (empty(array_intersect($addresses, $addresses))) {
            throw HttpException::Unauthorized($request);
        }
        return $next($request);
    }
}
