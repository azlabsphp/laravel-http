<?php

namespace Drewlabs\Packages\Http\Factory;

use Drewlabs\Http\Factory\AuthorizationErrorResponseFactoryInterface;
use Drewlabs\Packages\Http\ServerRequest;
use Throwable;
use Illuminate\Http\Request;

class AuthorizationErrorResponseFactory implements AuthorizationErrorResponseFactoryInterface
{
    use ContextResponseFactory;

    /**
     * Creates class instance
     * 
     * @param callable|\Closure($content = '', $status = 200, array $headers = []): \Symfony\Component\HttpFoundation\Response $factory 
     */
    public function __construct($factory = null)
    {
        $this->responseFactory = $factory ?? self::useDefault();
    }

    /**
     * {@inheritDoc}
     * 
     * @param Request $request
     */
    public function create($request, ?Throwable $exception = null)
    {
        $request = new ServerRequest($request);
        $message = $request->getMethod() . ' ' . $request->getPath() . '  Unauthorized access.' . ($exception ? ' [ERROR] : ' . $exception->getMessage() : '');
        return $this->createResponse($message, 401, []);
    }
}
