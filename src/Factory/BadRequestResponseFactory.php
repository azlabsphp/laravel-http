<?php

namespace Drewlabs\Packages\Http\Factory;

use Drewlabs\Http\Factory\BadRequestResponseFactoryInterface;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class BadRequestResponseFactory implements BadRequestResponseFactoryInterface
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
     * @return Response|HttpFoundationResponse
     */
    public function create(array $errors, array $headers = [])
    {
        return $this->createResponse($errors, 422, $headers ?? []);
    }
}
