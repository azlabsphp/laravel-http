<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Packages\Http\Exceptions\NotSupportedMessageException;
use Drewlabs\Packages\Http\Traits\HttpMessageTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Http\Response as HttpResponse;

/**
 * @package Drewlabs\Packages\Http
 */
class Response
{
    use HttpMessageTrait;

    /**
     *
     * @var ResponseInterface|SymfonyResponse
     */
    private $internal;

    public function __construct($response)
    {
        $this->internal = $response instanceof self ? $response->unwrap() : $response;
        if (!$this->isSupported()) {
            throw NotSupportedMessageException::forResponse($this->internal);
        }
    }

    public function setHeader(string $key, $value)
    {
        if ($this->isSymfony()) {
            $this->internal->headers->set($key, $value, true);
            return $this;
        }
        if ($this->isPsr7()) {
            $this->internal = $this->internal->withHeader($key, $value);
            return $this;
        }
        throw NotSupportedMessageException::forResponse($this->internal);
    }

    public function isSupported()
    {
        return $this->isSymfony() || $this->isPsr7();
    }

    /**
     * 
     * @param mixed $response 
     * @return self 
     * @throws InvalidArgumentException 
     */
    public static function wrap($response)
    {
        return new self($response);
    }

    /**
     * Return the wrapped response object
     * 
     * @return ResponseInterface|HttpResponse|SymfonyResponse 
     */
    public function unwrap()
    {
        return $this->internal;
    }

    public function getStatusCode()
    {
        if ($this->isSupported()) {
            return $this->internal->getStatusCode();
        }
        throw NotSupportedMessageException::forResponse($this->internal);
    }

    private function isSymfony()
    {
        return $this->internal instanceof HttpResponse || $this->internal instanceof SymfonyResponse;
    }

    private function isPsr7()
    {
        return $this->internal instanceof ResponseInterface;
    }
}
