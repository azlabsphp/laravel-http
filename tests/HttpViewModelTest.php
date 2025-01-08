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
use Drewlabs\Laravel\Http\Tests\TestViewModel;
use Drewlabs\Psr7\CreatesJSONStream;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class HttpViewModelTest extends TestCase
{
    public function test_view_model_property_getter()
    {
        $view = new TestViewModel($this->createRequestInstance());
        $this->assertSame('Environment', $view->title);
        $this->assertSame('Environment Posts', $view->content);
    }

    public function test_view_model_interacts_with_request_request_method()
    {
        $request = $this->createRequestInstance();
        $view = new TestViewModel($request);
        $this->assertSame($request, $view->request());
    }

    public function test_view_model_bearer_token_method()
    {
        $jwtToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';
        $request = $this->createRequestInstance(['Authorization' => "Bearer $jwtToken"]);
        $view = new TestViewModel($request);
        $this->assertSame($jwtToken, $view->bearerToken());
    }

    public function test_view_model_basic_auth_method()
    {
        $username = 'JohnDoe';
        $password = 'MyPassword';
        $request = $this->createRequestInstance(['Authorization' => 'Basic ' . base64_encode("$username:$password")]);
        $view = new TestViewModel($request);
        [$u, $p] = $view->basicAuth();
        $this->assertSame($username, $u);
        $this->assertSame($password, $p);
    }

    public function test_has_authenticatable_user_resolver()
    {
        $user = uniqid('id') . time();
        $password = 'MyPassword';
        $request = $this->createRequestInstance(['Authorization' => 'Basic ' . base64_encode("$user:$password")]);
        $view = new TestViewModel($request);
        $view = $view->setUserResolver(static function () use ($view) {
            [$user] = $view->basicAuth();

            return $user;
        });
        $this->assertSame($user, $view->user());
    }

    private function createRequestInstance(array $headers = [])
    {
        $factory = new LaravelRequestFactory();
        $jsonStreamFactory = new CreatesJSONStream(['title' => 'Environment', 'content' => 'Environment Posts']);

        return $factory->create(
            new ServerRequest(
                'POST',
                'http://127.0.0.1:8000/api/posts',
                array_merge(['Content-Type' => 'application/json', 'Accept' => '*/*'], $headers),
                $jsonStreamFactory->createStream()
            )
        );
    }
}
