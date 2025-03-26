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

use Drewlabs\Laravel\Http\Exceptions\HttpException;

final class PostSize
{
    /**
     * Handle an incoming request.
     *
     * @template TResponse
     *
     * @param mixed $request
     *
     * @throws HttpException
     *
     * @return TResponse
     */
    public function handle($request, \Closure $next, ?int $size = null)
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
        if (is_numeric($postMaxSize = \ini_get('post_max_size'))) {
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
