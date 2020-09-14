<?php

namespace Drewlabs\Packages\Http\Middleware;

use Closure;

class IPWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$ip_whitelist)
    {
        if (!is_null($request->headers->get('X-Real-IP'))) {
            $remote_address = $request->headers->get('X-Real-IP');
        } else {
            $remote_address = $request->ip();
        }
        if (!\in_array($remote_address, $ip_whitelist)) {
            $message = $request->method() . ' ' . $request->path() . '  Unauthorized access. You don\'t have the required privileges to access the ressource.';
            if (function_exists('response')) {
                return call_user_func_array('response', array($message, 401));
            }
            throw new \RuntimeException('response handler method not defined...', 500);
        }
        return $next($request);
    }
}
