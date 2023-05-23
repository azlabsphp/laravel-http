<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Http\Factory\BadRequestResponseFactoryInterface;
use Drewlabs\Http\Factory\OkResponseFactoryInterface;
use Drewlabs\Http\Factory\ResponseFactoryInterface;
use Drewlabs\Http\Factory\ServerErrorResponseFactoryInterface;
use Drewlabs\Packages\Http\Contracts\ResponseHandler;
use Drewlabs\Packages\Http\Factory\BadRequestResponseFactory;
use Drewlabs\Packages\Http\Factory\LaravelResponseFactory;
use Drewlabs\Packages\Http\Factory\OkResponseFactory;
use Drewlabs\Packages\Http\Factory\ServerErrorResponseFactory;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Throwable;

final class JsonResponseHandler implements ResponseHandler
{
    /**
     * @var OkResponseFactoryInterface
     */
    private $okResponseFactory;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var ServerErrorResponseFactoryInterface
     */
    private $serverErrorResponseFactory;

    /**
     * @var BadRequestResponseFactoryInterface
     */
    private $badRequestResponseFactory;

    /**
     * Creates `json` response handler class instance
     * 
     * @param OkResponseFactoryInterface|null $okResponseFactory 
     * @param ResponseFactoryInterface|null $responseFactory 
     * @param ServerErrorResponseFactoryInterface|null $serverErrorResponseFactory 
     * @param BadRequestResponseFactoryInterface|null $badRequestResponseFactory
     */
    public function __construct(
        OkResponseFactoryInterface $okResponseFactory = null,
        ResponseFactoryInterface $responseFactory = null,
        ServerErrorResponseFactoryInterface $serverErrorResponseFactory = null,
        BadRequestResponseFactoryInterface $badRequestResponseFactory = null
    ) {
        $jsonFactory = function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        };
        $this->okResponseFactory = $okResponseFactory ?? new OkResponseFactory($jsonFactory);
        $this->responseFactory = $responseFactory ?? new LaravelResponseFactory($jsonFactory);
        $this->serverErrorResponseFactory = $serverErrorResponseFactory ?? new ServerErrorResponseFactory($jsonFactory);
        $this->badRequestResponseFactory = $badRequestResponseFactory ?? new BadRequestResponseFactory($jsonFactory);
    }

    public function respond($data, int $status, array $headers = [])
    {
        return $this->responseFactory->create($data, $status, $headers, '1.1');
    }

    public function ok($data, array $errors = null, $success = true)
    {
        return $this->okResponseFactory->create($data, []);
    }

    public function error(Throwable $e, $errors = null)
    {
        $this->serverErrorResponseFactory->create(new RuntimeException('Server Error', 500, $e));
    }

    public function badRequest(array $errors)
    {
        return $this->badRequestResponseFactory->create($errors);
    }
}
