<?php

namespace Drewlabs\Packages\Http\Factory;

use Drewlabs\Http\Factory\OkResponseFactoryInterface;

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
        $this->responseFactory = $factory ?? self::useDefault();
    }

    public function create($data, array $headers = [])
    {
        return $this->createResponse($data, 200, $headers ?? []);
    }
}
