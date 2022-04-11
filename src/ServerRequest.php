<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Packages\Http\Exceptions\UnsupportedTypeException;
use Drewlabs\Packages\Http\Traits\HttpMessageTrait;
use Illuminate\Http\Request as HttpRequest;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package Drewlabs\Packages\Http
 */
class ServerRequest
{
    use HttpMessageTrait;

    /**
     *
     * @var ServerRequestInterface|Request|HttpRequest|mixed
     */
    private $internal;

    public function __construct($request)
    {
        $this->internal =  $request instanceof self ? $request->unwrap() : $request;
        if (!$this->isSupported()) {
            throw UnsupportedTypeException::forRequest($this->internal);
        }
    }

    /**
     * Get the current path info for the request.
     * 
     * @return string 
     * @throws SuspiciousOperationException 
     * @throws ConflictingHeadersException 
     * @throws UnsupportedTypeException 
     */
    public function path()
    {
        if ($this->isPsr7()) {
            $uri = $this->internal->getUri();
            $path = $uri->getPath();
            return $path === '' ? '/' : $path;
        }
        if ($this->isSymfony()) {
            $pattern = trim($this->internal->getPathInfo(), '/');
            return $pattern === '' ? '/' : $pattern;
        }
        throw UnsupportedTypeException::forRequest($this->internal);
    }

    public function setMethod(string $method)
    {
        if ($this->isSymfony()) {
            $this->internal->setMethod($method);
            return $this;
        }
        if ($this->isPsr7()) {
            $this->internal = $this->internal->withMethod('OPTIONS');
            return $this;
        }
        throw UnsupportedTypeException::forResponse($this->response);
    }

    /**
     * 
     * @return string 
     */
    public function getMethod()
    {
        if ($this->isSupported()) {
            return $this->internal->getMethod();
        }
        throw UnsupportedTypeException::forRequest($this->internal);
    }

    /**
     * 
     * @param string $method 
     * @return bool 
     * @throws SuspiciousOperationException 
     * @throws UnsupportedTypeException 
     */
    public function isMethod(string $method)
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }

    /**
     * 
     * @param mixed $request 
     * @return self 
     * @throws InvalidArgumentException 
     */
    public static function wrap($request)
    {
        return new self($request);
    }

    /**
     * Return the wrapped request object
     * 
     * @return ServerRequestInterface|HttpRequest|Request 
     */
    public function unwrap()
    {
        return $this->internal;
    }

    public function isSupported()
    {
        return $this->isSymfony() || $this->isPsr7();
    }


    private function isSymfony()
    {
        return $this->internal instanceof Request || $this->internal instanceof HttpRequest;
    }

    private function isPsr7()
    {
        return $this->internal instanceof ServerRequestInterface;
    }
}
