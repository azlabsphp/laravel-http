<?php

namespace Drewlabs\Packages\Http\Factory;

use Drewlabs\Http\Factory\OkResponseFactoryInterface;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class OkResponseFactory implements OkResponseFactoryInterface
{
    use ContextResponseFactory;

    /**
     * Creates class instance
     * 
     * @param callable|\Closure($content = '', $status = 200, array $headers = []): \Symfony\Component\HttpFoundation\Response $factory 
     */
    public function __construct(callable $factory = null)
    {
        $this->responseFactory = $factory ?? self::useDefaultFactory();
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
