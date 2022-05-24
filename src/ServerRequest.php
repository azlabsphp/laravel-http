<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Packages\Http\Exceptions\UnsupportedTypeException;
use Drewlabs\Packages\Http\Traits\HttpMessageTrait;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

/**
 * @package Drewlabs\Packages\Http
 */
class ServerRequest
{
    use HttpMessageTrait;

    /**
     * @var string[]
     */
    private const TRUSTED_HEADERS = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    /**
     *
     * @var \Psr\Http\Message\ServerRequestInterface|\Symfony\Component\HttpFoundation\Request|\Illuminate\Http\Request|mixed
     */
    private $internal;

    public function __construct($request = null)
    {
        if (null === $request) {
            $this->createFromServerGlobals();
        } else {
            $this->setRequest($request);
        }
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

    /**
     * Override the HTTP verb which is used to send the message
     * 
     * @param string $method 
     * @return self 
     * @throws InvalidArgumentException 
     * @throws UnsupportedTypeException 
     */
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
     * Returns the HTTP verb used in sending request to server
     * 
     * @return string 
     * @throws InvalidArgumentException 
     * @throws BadRequestException 
     * @throws SuspiciousOperationException 
     * @throws UnsupportedTypeException 
     */
    public function getMethod()
    {
        if ($this->isSupported()) {
            return $this->internal->getMethod();
        }
        throw UnsupportedTypeException::forRequest($this->internal);
    }

    /**
     * Match the HTPP verb against the one provided by user
     * 
     * @param string $method 
     * @return bool 
     * @throws InvalidArgumentException 
     * @throws BadRequestException 
     * @throws SuspiciousOperationException 
     * @throws UnsupportedTypeException 
     */
    public function isMethod(string $method)
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }


    /**
     * Creates an instance of the current class wrapping a library or framework specific client
     * 
     * @param mixed $request 
     * @return ServerRequest 
     */
    public static function wrap($request)
    {
        return new self($request);
    }

    /**
     * Returns the internal request object being used by the current class
     * 
     * @return mixed 
     */
    public function unwrap()
    {
        return $this->internal;
    }

    /**
     * Checks if the wrapped library request class is supported or not
     * 
     * @return bool 
     */
    public function isSupported()
    {
        return $this->isSymfony() || $this->isPsr7();
    }

    /**
     * Get the client IP address.
     * 
     * @return mixed 
     * @throws UnsupportedTypeException 
     * @throws ConflictingHeadersException 
     */
    public function ip()
    {
        if (!$this->isSupported()) {
            throw UnsupportedTypeException::forRequest($this->internal);
        }
        return is_array($addresses = $this->ips()) ? Arr::first($addresses) : $addresses;
    }

    /**
     * Get the client IP addresses.
     * 
     * @return array 
     * @throws UnsupportedTypeException 
     * @throws ConflictingHeadersException 
     */
    public function ips(): array
    {
        if (!$this->isSupported()) {
            throw UnsupportedTypeException::forRequest($this->internal);
        }
        if ($this->isPsr7()) {
            return $this->getPsr7Ips();
        }
        return $this->internal->getClientIps();
    }

    /**
     * Get a server key from the request server array
     * 
     * @param string|null $key 
     * @return string|array 
     * @throws UnsupportedTypeException 
     * @throws BadRequestException 
     */
    public function server(string $key = null)
    {
        if (!$this->isSupported()) {
            throw UnsupportedTypeException::forRequest($this->internal);
        }
        if ($this->isPsr7()) {
            return $this->psrServer($key);
        }
        return $key ? $this->internal->server->get($key) : $this->internal->server->all();
    }

    /**
     * 
     * @return array 
     */
    private function getPsr7Ips()
    {
        $ips = [];
        foreach (static::TRUSTED_HEADERS as $key) {
            $attribute = is_array($value = $this->psrServer($key)) ? Arr::first($value) : $value;
            foreach (array_map('trim', explode(',', $attribute)) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    $ips[] = $ip;
                }
            }
        }
        return array_unique($ips);
    }

    /**
     * 
     * @param string $key 
     * @return array|string 
     */
    private function psrServer(string $key)
    {
        $server = $this->internal->getServerParams() ?? [];
        return $key ? $server[$key] ?? null : $server;
    }

    /**
     * 
     * @return bool 
     */
    private function isSymfony()
    {
        return $this->internal instanceof \Symfony\Component\HttpFoundation\Request ||
            $this->internal instanceof \Illuminate\Http\Request;
    }

    /**
     * 
     * @return bool 
     */
    private function isPsr7()
    {
        return $this->internal instanceof \Psr\Http\Message\ServerRequestInterface;
    }

    /**
     * 
     * @return void 
     * @throws InvalidArgumentException 
     */
    private function createFromServerGlobals()
    {
        if (class_exists(\Nyholm\Psr7\Factory\Psr17Factory::class) && class_exists(\Nyholm\Psr7Server\ServerRequestCreator::class)) {
            $this->internal = drewlabs_create_psr7_request();
        }
    }

    /**
     * 
     * @param mixed $request 
     * @return void 
     */
    private function setRequest($request)
    {
        $this->internal =  $request instanceof self ? $request->unwrap() : $request;
    }
}
