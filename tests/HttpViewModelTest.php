<?php

use Drewlabs\Packages\Http\Factory\LaravelRequestFactory;
use Drewlabs\Packages\Http\Tests\TestViewModel;
use Drewlabs\Psr7\CreatesJSONStream;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class HttpViewModelTest extends TestCase
{

    private function createRequestInstance(array $headers = [])
    {
        $factory = new LaravelRequestFactory;
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

    public function test_view_model_property_getter()
    {
        $view = new TestViewModel($this->createRequestInstance());
        $this->assertEquals('Environment', $view->title);
        $this->assertEquals('Environment Posts', $view->content);
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
        $this->assertEquals($jwtToken, $view->bearerToken());
    }


    public function test_view_model_basic_auth_method()
    {
        $username = 'JohnDoe';
        $password = 'MyPassword';
        $request = $this->createRequestInstance(['Authorization' => "Basic " . base64_encode("$username:$password")]);
        $view = new TestViewModel($request);
        list($u, $p) = $view->basicAuth();
        $this->assertEquals($username, $u);
        $this->assertEquals($password, $p);
    }


    public function test_has_authenticatable_user_resolver()
    {
        $user = uniqid('id').time();
        $password = 'MyPassword';
        $request = $this->createRequestInstance(['Authorization' => "Basic " . base64_encode("$user:$password")]);
        $view = new TestViewModel($request);
        $view = $view->setUserResolver(function() use ($view) {
            list($user) = $view->basicAuth();
            return $user;
        });
        $this->assertEquals($user, $view->user());

    }
}
