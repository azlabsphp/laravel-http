<?php

namespace Drewlabs\Packages\Http\Middleware;

use Closure;
use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Packages\Http\Exceptions\HttpAuthorizationException;

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
        $remote_address = null !== $request->headers->get('X-Real-IP') ?
            Arr::wrap($request->headers->get('X-Real-IP')) :
            $request->ips();
        if (empty(array_intersect($remote_address, $addresses))) {
            throw new HttpAuthorizationException($request);
        }
        return $next($request);
    }
}
