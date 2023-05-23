<?php

use Drewlabs\Packages\Http\Factory\LaravelRequestFactory;
use Drewlabs\Psr7\CreatesJSONStream;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class LaravelRequestFactoryTest extends TestCase
{
    public function test_laravel_request_factory_create()
    {
        $factory = new LaravelRequestFactory;
        $request = $factory->create(new ServerRequest('GET', 'http://127.0.0.1:8000/api/posts', ['Content-Type' => 'application/json', 'Accept' => '*/*']));
        $this->assertEquals('api/posts', $request->path());
        $this->assertTrue($request->is('api/*'));
        $this->assertTrue($request->isMethod('GET'));
        $this->assertEquals('application/json', $request->header('Content-Type'));
    }

    public function test_laravel_request_factory_create_post_request()
    {
        $factory = new LaravelRequestFactory;
        $jsonStreamFactory = new CreatesJSONStream(['title' => 'Environment', 'content' => 'Environment Posts']);
        $request = $factory->create(
            new ServerRequest(
                'POST',
                'http://127.0.0.1:8000/api/posts',
                ['Content-Type' => 'application/json', 'Accept' => '*/*'],
                $jsonStreamFactory->createStream()
            )
        );
        $this->assertEquals('api/posts', $request->path());
        $this->assertTrue($request->is('api/*'));
        $this->assertTrue($request->isMethod('POST'));
        $this->assertEquals('application/json', $request->header('Content-Type'));
        $this->assertEquals('Environment', $request->input('title'));
    }
}
