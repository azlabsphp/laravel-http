<?php

namespace Drewlabs\Packages\Http\Middleware;

use Closure;
use Drewlabs\Packages\Http\Exceptions\HttpException;

/**
 * 
 * @package Drewlabs\Packages\Http\Middleware
 */
final class ValidatePostSize
{
    /**
     * Handle an incoming request.
     *
     * @param  mixed  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws HttpException
     */
    public function handle($request, Closure $next)
    {
        $max = $this->getPostMaxSize();
        if ($max > 0 && $request->server('CONTENT_LENGTH') > $max) {
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
