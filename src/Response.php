<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Packages\Http\Traits\HttpMessageTrait;
use Illuminate\Http\Response as HttpResponse;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class Response
{
    use HttpMessageTrait;

    /**
     * @var HttpResponse|HttpFoundationResponse
     */
    private $internal;

    /**
     * Creates new class instance
     * 
     * @param HttpResponse|HttpFoundationResponse|self $response 
     */
    public function __construct($response)
    {
        $this->internal = $response instanceof self ? $response->unwrap() : $response;
        $this->throwIfNotExpected();
    }

    public function setHeader(string $key, $value)
    {
        $this->internal->headers->set($key, $value, true);
        return $this;
    }

    /**
     * Wrap http foundation response into the current response
     * 
     * @param mixed $response 
     * @return Response 
     */
    public static function wrap($response)
    {
        return new self($response);
    }

    /**
     * Return the wrapped response object
     * 
     * @return HttpResponse|HttpFoundationResponse 
     */
    public function unwrap()
    {
        return $this->internal;
    }

    public function getStatusCode()
    {
        return $this->internal->getStatusCode();
    }

    private function throwIfNotExpected()
    {
        if (!($this->internal instanceof HttpResponse || $this->internal instanceof HttpFoundationResponse)) {
            throw new InvalidArgumentException('Not supported response instance');
        }
    }
}
