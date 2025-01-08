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

use Drewlabs\Http\Factory\OkResponseFactoryInterface;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class OkResponseFactory implements OkResponseFactoryInterface
{
    use ContextResponseFactory;

    /**
     * Creates class instance.
     *
     * @param callable|\Closure($content = '', $status = 200, array $headers = []): \Symfony\Component\HttpFoundation\Response $factory
     */
    public function __construct(?callable $factory = null)
    {
        $this->responseFactory = $factory ?? static::useDefaultFactory();
    }

    /**
     * {@inheritDoc}
     *
     * @return Response|HttpFoundationResponse
     */
    public function create($data, array $headers = [])
    {
        return $this->createResponse($data, 200, $headers ?? []);
    }
}
