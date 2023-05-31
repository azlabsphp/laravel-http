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

use Drewlabs\Laravel\Http\Factory\OkResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class OkResponseFactoryTest extends TestCase
{
    public function test_ok_response_create()
    {
        $factory = new OkResponseFactory();
        $this->assertInstanceOf(Response::class, $response = $factory->create([], ['Content-Type' => 'application/json', 'Accept' => '*/*']));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_ok_response_create_overrides_default_response_factory()
    {
        $factory = new OkResponseFactory(static function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
        });
        $this->assertInstanceOf(JsonResponse::class, $response = $factory->create(['data' => []], ['Content-Type' => 'application/json', 'Accept' => '*/*']));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode(['data' => []], \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES), $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }
}
