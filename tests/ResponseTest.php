<?php

use Drewlabs\Packages\Http\Response;
use Illuminate\Http\Response as HttpResponse;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    public function test_get_header()
    {
        $response = Response::wrap(new HttpResponse('', 200, [
            'Access-Control-Allow-Origin' => 'http://localhost'
        ]));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('http://localhost', $response->getHeader('Access-Control-Allow-Origin'));
    }

    public function test_get_status_code()
    {
        $response = Response::wrap(new HttpResponse('', 422));
        $this->assertNotEquals(200, $response->getStatusCode());
        $this->assertEquals(422, $response->getStatusCode());
    }

}