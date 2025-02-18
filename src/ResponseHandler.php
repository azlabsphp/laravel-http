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
use Drewlabs\Laravel\Http\Contracts\ResponseHandler as AbstractResponseHandler;
use Drewlabs\Laravel\Http\Factory\BadRequestResponseFactory;
use Drewlabs\Laravel\Http\Factory\LaravelResponseFactory;
use Drewlabs\Laravel\Http\Factory\OkResponseFactory;
use Drewlabs\Laravel\Http\Factory\ServerErrorResponseFactory;

final class ResponseHandler implements AbstractResponseHandler
{
    /** @var OkResponseFactoryInterface */
    private $okResponseFactory;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ServerErrorResponseFactoryInterface */
    private $serverErrorResponseFactory;

    /** @var BadRequestResponseFactoryInterface */
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
        $this->okResponseFactory = $okResponseFactory ?? new OkResponseFactory();
        $this->responseFactory = $responseFactory ?? new LaravelResponseFactory();
        $this->serverErrorResponseFactory = $serverErrorResponseFactory ?? new ServerErrorResponseFactory();
        $this->badRequestResponseFactory = $badRequestResponseFactory ?? new BadRequestResponseFactory();
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
