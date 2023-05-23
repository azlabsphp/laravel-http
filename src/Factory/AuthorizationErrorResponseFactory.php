<?php

namespace Drewlabs\Packages\Http\Factory;

use Drewlabs\Http\Factory\AuthorizationErrorResponseFactoryInterface;
use Drewlabs\Packages\Http\ServerRequest;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Illuminate\Http\Response;

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
        $this->responseFactory = $factory ?? self::useDefaultFactory();
    }

    /**
     * {@inheritDoc}
     * 
     * @param Request $request
     * 
     * @return HttpFoundationResponse|Response
     */
    public function create($request, ?Throwable $exception = null)
    {
        $request = new ServerRequest($request);
        $message = $request->getMethod() . ' ' . $request->getPath() . '  Unauthorized access.' . ($exception ? ' [ERROR] : ' . $exception->getMessage() : '');
        return $this->createResponse($message, 401, []);
    }
}
