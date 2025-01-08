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

use Drewlabs\Http\Factory\AuthorizationErrorResponseFactoryInterface;
use Drewlabs\Laravel\Http\ServerRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class AuthorizationErrorResponseFactory implements AuthorizationErrorResponseFactoryInterface
{
    use ContextResponseFactory;

    /**
     * Creates class instance.
     *
     * @param callable|\Closure($content = '', $status = 200, array $headers = []): \Symfony\Component\HttpFoundation\Response $factory
     */
    public function __construct($factory = null)
    {
        $this->responseFactory = $factory ?? static::useDefaultFactory();
    }

    /**
     * {@inheritDoc}
     *
     * @param Request $request
     *
     * @return HttpFoundationResponse|Response
     */
    public function create($request, ?\Throwable $exception = null)
    {
        $request = new ServerRequest($request);
        $message = $request->getMethod().' '.$request->getPath().'  Unauthorized access.'.($exception ? ' [ERROR] : '.$exception->getMessage() : '');

        return $this->createResponse($message, 401, []);
    }
}
