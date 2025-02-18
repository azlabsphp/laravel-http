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

namespace Drewlabs\Laravel\Http;

use Drewlabs\Http\Factory\BadRequestResponseFactoryInterface;
use Drewlabs\Http\Factory\OkResponseFactoryInterface;
use Drewlabs\Http\Factory\ResponseFactoryInterface;
use Drewlabs\Http\Factory\ServerErrorResponseFactoryInterface;
use Drewlabs\Laravel\Http\Contracts\ResponseHandler;
use Drewlabs\Laravel\Http\Factory\BadRequestResponseFactory;
use Drewlabs\Laravel\Http\Factory\LaravelResponseFactory;
use Drewlabs\Laravel\Http\Factory\OkResponseFactory;
use Drewlabs\Laravel\Http\Factory\ServerErrorResponseFactory;
use Illuminate\Http\JsonResponse;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;

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
     * Creates `json` response handler class instance.
     */
    public function __construct(
        ?OkResponseFactoryInterface $okResponseFactory = null,
        ?ResponseFactoryInterface $responseFactory = null,
        ?ServerErrorResponseFactoryInterface $serverErrorResponseFactory = null,
        ?BadRequestResponseFactoryInterface $badRequestResponseFactory = null
    ) {
        $jsonFactory = static function ($data = null, $status = 200, $headers = []) {
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

    public function ok($data, ?array $errors = null, $success = true)
    {
        return $this->okResponseFactory->create($data, []);
    }

    public function error(\Throwable $e, $errors = null)
    {
        $this->serverErrorResponseFactory->create(new \RuntimeException('Server Error', 500, $e));
    }

    public function badRequest(array $errors)
    {
        return $this->badRequestResponseFactory->create($errors);
    }
}
