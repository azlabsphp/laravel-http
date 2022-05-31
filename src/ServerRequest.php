<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Packages\Http\Exceptions\NotSupportedMessageException;
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
            throw NotSupportedMessageException::forRequest($this->internal);
        }
    }

    /**
     * Get the current path info for the request.
     * 
     * @return string 
     * @throws SuspiciousOperationException 
     * @throws ConflictingHeadersException 
     * @throws NotSupportedMessageException 
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
        throw NotSupportedMessageException::forRequest($this->internal);
    }

    /**
     * Override the HTTP verb which is used to send the message
     * 
     * @param string $method 
     * @return self 
     * @throws InvalidArgumentException 
     * @throws NotSupportedMessageException 
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
        throw NotSupportedMessageException::forResponse($this->response);
    }

    /**
     * Returns the HTTP verb used in sending request to server
     * 
     * @return string 
     * @throws InvalidArgumentException 
     * @throws BadRequestException 
     * @throws SuspiciousOperationException 
     * @throws NotSupportedMessageException 
     */
    public function getMethod()
    {
        return $this->internal->getMethod();
    }

    /**
     * Match the HTPP verb against the one provided by user
     * 
     * @param string $method 
     * @return bool 
     * @throws InvalidArgumentException 
     * @throws BadRequestException 
     * @throws SuspiciousOperationException 
     * @throws NotSupportedMessageException 
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
     * @throws NotSupportedMessageException 
     * @throws ConflictingHeadersException 
     */
    public function ip()
    {
        $request = is_array($addresses = $this->ips()) ? Arr::first($addresses) : $addresses;
        return empty($request) ? $this->getHeader('X-Real-IP') : $request;
    }

    /**
     * Get the client IP addresses.
     * 
     * @return array 
     * @throws NotSupportedMessageException 
     * @throws ConflictingHeadersException 
     */
    public function ips(): array
    {
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
     * @throws NotSupportedMessageException 
     * @throws BadRequestException 
     */
    public function server(string $key = null)
    {
        if ($this->isPsr7()) {
            return $this->psrServer($key);
        }
        return $key ? $this->internal->server->get($key) : $this->internal->server->all();
    }

    /**
     * Gets cookie value from the user provided name
     * @param string $name 
     * @return string|array 
     * @throws InvalidArgumentException 
     * @throws BadRequestException 
     */
    public function cookie(string $name = null)
    {
        if ($this->isPsr7()) {
            return $this->getPsr7Cookie($name);
        }
        return is_string($name) ? $this->internal->cookies->get($name) : $this->internal->cookies->all();
    }

    /**
     * Returns the value of the request query parameters
     * 
     * @param string|null $name 
     * @return mixed 
     * @throws SuspiciousOperationException 
     * @throws ConflictingHeadersException 
     */
    public function query(string $name = null)
    {
        if ($this->isPsr7()) {
            return $this->getPsr7Query($name);
        }
        return $name ? Arr::get($this->internal->query->all() ?? [], $name) : ($this->internal->query->all() ?? []);
    }

    /**
     * Query for value from request parsed body
     * 
     * @param string|null $name 
     * @return mixed 
     */
    public function input(string $name = null)
    {
        if ($this->isPsr7()) {
            return $this->getPsr7ParsedBody($name);
        }
        $input = $this->getAllSymfonyInputs();
        return $name ? Arr::get($input, $name) : $input;
    }


    /**
     * Returns an array containing all request input or user provided keys
     * 
     * @param array ...$keys 
     * @return array 
     * @throws SuspiciousOperationException 
     * @throws ConflictingHeadersException 
     * @throws BadRequestException 
     */
    public function all($keys = [])
    {
        $input = $this->isPsr7() ? $this->getPsr7AllInputs() : $this->getAllSymfonyInputs();
        if (empty($keys)) {
            return $input;
        }
        $out = [];
        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            Arr::set($out, $key, Arr::get($input, $key));
        }
        return $out;
    }

    /**
     * 
     * @param string|null $name 
     * @return mixed 
     */
    private function getPsr7Cookie(string $name = null)
    {
        $cookies = $this->internal->getCookieParams() ?? [];
        return is_string($name) ? Arr::get($cookies ?? [], $name) : ($cookies ?? []);
    }

    /**
     * 
     * @param string|null $name 
     * @return string|array 
     * @throws SuspiciousOperationException 
     * @throws ConflictingHeadersException 
     */
    private function getPsr7Query(string $name = null)
    {
        // First we read query parameters from Psr7 request queryParams bag
        $params = $this->internal->getQueryParams();
        $components = parse_url($this->internal->getUri()->__toString());
        // Then we attempt to parse request url make sure query string values
        // are included in the query parameters
        if ($components) {
            $query = [];
            parse_str(str_replace('?', '&', $components['query'] ?? ''), $query);
            $params = array_merge(
                $params,
                array_filter($query, function ($value) {
                    return !empty($value);
                })
            );
        }
        return $name ? Arr::get($params ?? [], $name) : ($params ?? []);
    }

    /**
     * 
     * @param string|null $name 
     * @return mixed 
     */
    private function getPsr7ParsedBody(string $name = null)
    {
        $body = $this->internal->getParsedBody();
        return $name ? Arr::get($body ?? [], $name) : ($body ?? []);
    }

    /**
     * 
     * @return array 
     * @throws SuspiciousOperationException 
     * @throws ConflictingHeadersException 
     */
    private function getPsr7AllInputs()
    {
        return array_merge(
            $this->getPsr7Query(),
            $this->getPsr7ParsedBody()
        );
    }

    /**
     * 
     * @return array 
     * @throws BadRequestException 
     */
    private function getAllSymfonyInputs()
    {
        return array_merge(
            $this->internal->getInputSource()->all() ?? [],
            $this->internal->query->all() ?? []
        );
    }

    /**
     * 
     * @return array 
     */
    private function getPsr7Ips()
    {
        $addresses = [];
        foreach (static::TRUSTED_HEADERS as $key) {
            $attribute = is_array($value = $this->psrServer($key)) ? Arr::first($value) : $value;
            if (null === $attribute) {
                continue;
            }
            foreach (array_map('trim', explode(',', $attribute)) as $addr) {
                if (filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    $addresses[] = $addr;
                }
            }
        }
        return array_unique($addresses);
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
