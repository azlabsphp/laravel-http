<?php

namespace Drewlabs\Packages\Http\Middleware;

use Closure;
use Drewlabs\Packages\Http\Exceptions\HttpException;

/**
 * @package Drewlabs\Packages\Http\Middleware
 */
final class PostSize
{
    /**
     * Handle an incoming request.
     * 
     * @template TResponse
     *
     * @param  mixed  $request
     * @param  \Closure  $next
     * @param int $size
     * 
     * @return TResponse
     *
     * @throws HttpException
     */
    public function handle($request, Closure $next, int $size = null)
    {
        if (($max = $size ?? $this->getPostMaxSize()) > 0 && $request->server('CONTENT_LENGTH') > $max) {
            throw new HttpException(413);
        }
        return $next($request);
    }

    /**
     * Determine the server 'post_max_size' as bytes.
     *
     * @return int
     */
    private function getPostMaxSize()
    {
        if (is_numeric($postMaxSize = ini_get('post_max_size'))) {
            return (int) $postMaxSize;
        }
        $metric = strtoupper(substr($postMaxSize, -1));
        $postMaxSize = (int) $postMaxSize;
        switch ($metric) {
            case 'K':
                return $postMaxSize * 1024;
            case 'M':
                return $postMaxSize * 1048576;
            case 'G':
                return $postMaxSize * 1073741824;
            default:
                return $postMaxSize;
        }
    }
}
