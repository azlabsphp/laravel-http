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

namespace Drewlabs\Laravel\Http\Tests;

use Drewlabs\Laravel\Http\Factory\ServerErrorResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class ServerErrorResponseFactoryTest extends TestCase
{
    public function test_server_error_response_create()
    {
        $factory = new ServerErrorResponseFactory();
        $this->assertInstanceOf(Response::class, $response = $factory->create(new \RuntimeException('Server Error.')));
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('Server Error.', $response->getContent());
    }

    public function test_server_error_response_create_overrides_default_response_factory()
    {
        $factory = new ServerErrorResponseFactory(static function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
        });
        $this->assertInstanceOf(JsonResponse::class, $response = $factory->create(new \RuntimeException('Server Error.')));
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('"Server Error."', $response->getContent());
    }
}
