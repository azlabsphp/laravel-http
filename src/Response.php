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

use BadMethodCallException;
use Drewlabs\Laravel\Http\Traits\HttpMessageTrait;
use Error;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class Response
{
    use HttpMessageTrait;

    /**
     * Creates new class instance.
     *
     * @param HttpResponse|HttpFoundationResponse|self $response
     */
    public function __construct($response)
    {
        $this->message = $response instanceof self ? $response->unwrap() : $response;
        $this->throwIfNotExpected();
    }

    public function setHeader(string $key, $value)
    {
        $this->message->headers->set($key, $value, true);

        return $this;
    }

    /**
     * Wrap http foundation response into the current response.
     *
     * @param mixed $response
     *
     * @return Response
     */
    public static function wrap($response)
    {
        return new static($response);
    }

    /**
     * Return the wrapped response object.
     *
     * @return HttpResponse|HttpFoundationResponse
     */
    public function unwrap()
    {
        return $this->message;
    }

    public function getStatusCode()
    {
        return $this->message->getStatusCode();
    }

    private function throwIfNotExpected()
    {
        if (!($this->message instanceof HttpResponse || $this->message instanceof HttpFoundationResponse)) {
            throw new \InvalidArgumentException('Not supported response instance');
        }
    }

    // Proxy implementation
    /**
     * @param mixed $name 
     * @param array|mixed $arguments 
     * @return mixed 
     * @throws Error 
     * @throws BadMethodCallException 
     */
    public function __call($name, $arguments)
    {
        return $this->proxy($this->message, $name, $arguments);
    }
}
