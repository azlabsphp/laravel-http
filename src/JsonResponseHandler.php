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
use Drewlabs\Packages\Http\Traits\ContainerAware;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Throwable;

class JsonResponseHandler implements ResponseHandler
{
    use ContainerAware;

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
     * @var bool
     */
    private $debug = false;

    /**
     * Creates `json` response handler class instance
     * 
     * @param bool $environment
     * @param OkResponseFactoryInterface|null $okResponseFactory 
     * @param ResponseFactoryInterface|null $responseFactory 
     * @param ServerErrorResponseFactoryInterface|null $serverErrorResponseFactory 
     * @param BadRequestResponseFactoryInterface|null $badRequestResponseFactory
     */
    public function __construct(
        bool $environment = false,
        OkResponseFactoryInterface $okResponseFactory = null,
        ResponseFactoryInterface $responseFactory = null,
        ServerErrorResponseFactoryInterface $serverErrorResponseFactory = null,
        BadRequestResponseFactoryInterface $badRequestResponseFactory = null
    ) {
        $jsonFactory = function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        };
        $this->debug = $environment;
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
        self::createResolver('log')()->error(sprintf('%s', $message = $e->getMessage()));
        $message = $this->debug ? $message : 'Server Error';
        $this->serverErrorResponseFactory->create(new RuntimeException($message, 500, $e));
    }

    public function badRequest(array $errors)
    {
        return $this->badRequestResponseFactory->create($errors);
    }
}
