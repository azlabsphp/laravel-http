<?php

namespace Drewlabs\Packages\Http\Middleware;

use Closure;
use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Illuminate\Http\JsonResponse;

class TrustedIpAddress
{
    /**
     * 
     * @var IActionResponseHandler
     */
    private $response;

    public function __construct(IActionResponseHandler $response = null)
    {
        $this->response = $response;
    }

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
            $message = $request->method() . ' ' . $request->path() . '  Unauthorized access. You don\'t have the required privileges to access the ressource.';
            return $this->response ? $this->response->unauthorized($request) : new JsonResponse($message, 401);
        }
        return $next($request);
    }
}
