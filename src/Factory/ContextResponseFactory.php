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

namespace Drewlabs\Laravel\Http\Factory;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

trait ContextResponseFactory
{
    /**
     * @var \Closure($content = '', = 200, array = []): \Symfony\Component\HttpFoundation\Response
     */
    private $responseFactory;

    /**
     * Creates a default response factory class.
     *
     * @return ContextResponseFactory
     */
    public static function useDefaultFactory()
    {
        return static function ($data = null, $status = 200, $headers = []) {
            return new Response($data, $status, $headers);
        };
    }

    /**
     * Creates response instance from provided parameters.
     *
     * @param string $content
     * @param int    $status
     *
     * @return HttpFoundationResponse
     */
    private function createResponse($content = '', $status = 200, array $headers = [])
    {
        return ($this->responseFactory)($content, $status, $headers);
    }
}
