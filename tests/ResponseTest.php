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

use Drewlabs\Laravel\Http\Response;
use Illuminate\Http\Response as HttpResponse;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function test_get_header()
    {
        $response = Response::wrap(new HttpResponse('', 200, [
            'Access-Control-Allow-Origin' => 'http://localhost',
        ]));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('http://localhost', $response->getHeader('Access-Control-Allow-Origin'));
    }

    public function test_get_status_code()
    {
        $response = Response::wrap(new HttpResponse('', 422));
        $this->assertNotSame(200, $response->getStatusCode());
        $this->assertSame(422, $response->getStatusCode());
    }
}
