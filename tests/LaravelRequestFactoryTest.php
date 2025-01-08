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

use Drewlabs\Laravel\Http\Factory\LaravelRequestFactory;
use Drewlabs\Psr7\CreatesJSONStream;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class LaravelRequestFactoryTest extends TestCase
{
    public function test_laravel_request_factory_create()
    {
        $factory = new LaravelRequestFactory();
        $request = $factory->create(new ServerRequest('GET', 'http://127.0.0.1:8000/api/posts', ['Content-Type' => 'application/json', 'Accept' => '*/*']));
        $this->assertSame('api/posts', $request->path());
        $this->assertTrue($request->is('api/*'));
        $this->assertTrue($request->isMethod('GET'));
        $this->assertSame('application/json', $request->header('Content-Type'));
    }

    public function test_laravel_request_factory_create_post_request()
    {
        $factory = new LaravelRequestFactory();
        $jsonStreamFactory = new CreatesJSONStream(['title' => 'Environment', 'content' => 'Environment Posts']);
        $request = $factory->create(
            new ServerRequest(
                'POST',
                'http://127.0.0.1:8000/api/posts',
                ['Content-Type' => 'application/json', 'Accept' => '*/*'],
                $jsonStreamFactory->createStream()
            )
        );
        $this->assertSame('api/posts', $request->path());
        $this->assertTrue($request->is('api/*'));
        $this->assertTrue($request->isMethod('POST'));
        $this->assertSame('application/json', $request->header('Content-Type'));
        $this->assertSame('Environment', $request->input('title'));
    }
}
