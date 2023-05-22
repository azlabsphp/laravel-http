<?php

namespace Drewlabs\Packages\Http\Factory;

use Drewlabs\Http\Factory\ServerErrorResponseFactoryInterface;
use Throwable;

class ServerErrorResponseFactory implements ServerErrorResponseFactoryInterface
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

    public function create(Throwable $e, $data = null)
    {
        return ($this->responseFactory)($e->getMessage(), 500, []);
    }
}
