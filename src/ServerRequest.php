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

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Laravel\Http\Exceptions\NotSupportedMessageException;
use Drewlabs\Laravel\Http\Traits\HttpMessageTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

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
        'REMOTE_ADDR',
    ];

    /**
     * @var Request|HttpFoundationRequest
     */
    private $internal;

    /**
     * Creates class instances.
     *
     * @param Request|HttpFoundationRequest|null $request
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($request = null)
    {
        $this->internal = null === $request ? $this->createFromServerGlobals() : ($request instanceof self ? $request->unwrap() : $request);
        $this->throwIfNotExcepted();
    }

    public function __clone()
    {
        $this->internal = clone $this->internal;
    }

    /**
     * Get the current path info for the request.
     *
     * @throws SuspiciousOperationException
     * @throws ConflictingHeadersException
     * @throws NotSupportedMessageException
     *
     * @return string
     */
    public function getPath()
    {
        $pattern = trim($this->internal->getPathInfo(), '/');

        return '' === $pattern ? '/' : $pattern;
    }

    /**
     * Override the HTTP verb which is used to send the message.
     *
     * @return self
     */
    public function withMethod(string $method)
    {
        // Clone the current request instance
        $self = clone $this;

        // Set the method value on the request instance
        $self->internal->setMethod($method);

        // Return the cloned instance
        return $self;
    }

    /**
     * Returns the HTTP verb used in sending request to server.
     *
     * @throws \InvalidArgumentException
     * @throws BadRequestException
     * @throws SuspiciousOperationException
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->internal->getMethod();
    }

    /**
     * Match the HTPP verb against the one provided by user.
     *
     * @throws \InvalidArgumentException
     * @throws BadRequestException
     * @throws SuspiciousOperationException
     *
     * @return bool
     */
    public function isMethod(string $method)
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }

    /**
     * Creates an instance of the current class with framework request.
     *
     * @param Request|HttpFoundationRequest $request
     *
     * @return self
     */
    public static function wrap($request)
    {
        return new self($request);
    }

    /**
     * Return the framework request instance.
     *
     * @return Request|HttpFoundationRequest
     */
    public function unwrap()
    {
        return $this->internal;
    }

    /**
     * Get the client IP address.
     *
     * @throws ConflictingHeadersException
     *
     * @return mixed
     */
    public function ip()
    {
        $request = \is_array($addresses = $this->ips()) ? Arr::first($addresses) : $addresses;

        return empty($request) ? $this->getHeader('X-Real-IP') : $request;
    }

    /**
     * Get the client IP addresses.
     *
     * @throws ConflictingHeadersException
     */
    public function ips(): array
    {
        return $this->internal->getClientIps();
    }

    /**
     * Get a server key from the request server array.
     *
     * @throws BadRequestException
     *
     * @return string|array
     */
    public function server(string $key = null)
    {
        return $key ? $this->internal->server->get($key) : $this->internal->server->all();
    }

    /**
     * Gets cookie value from the user provided name.
     *
     * @param string $name
     *
     * @throws BadRequestException
     *
     * @return string|array
     */
    public function cookie(string $name = null)
    {
        return \is_string($name) ? $this->internal->cookies->get($name) : $this->internal->cookies->all();
    }

    /**
     * Returns the value of the request query parameters.
     *
     * @throws SuspiciousOperationException
     * @throws ConflictingHeadersException
     *
     * @return mixed
     */
    public function query(string $name = null)
    {
        return $name ? Arr::get($this->internal->query->all() ?? [], $name) : ($this->internal->query->all() ?? []);
    }

    /**
     * Query for value from request parsed body.
     *
     * @return mixed
     */
    public function input(string $name = null)
    {
        $input = array_merge($this->internal->getInputSource()->all() ?? [], $this->internal->query->all() ?? []);

        return $name ? Arr::get($input, $name) : $input;
    }

    /**
     * Returns an array containing all request input or user provided keys.
     *
     * @param array ...$keys
     *
     * @throws SuspiciousOperationException
     * @throws ConflictingHeadersException
     * @throws BadRequestException
     *
     * @return array
     */
    public function all($keys = [])
    {
        $input = array_merge($this->internal->getInputSource()->all() ?? [], $this->internal->query->all() ?? []);
        $output = [];
        foreach (\is_array($keys) ? $keys : \func_get_args() as $key) {
            Arr::set($output, $key, Arr::get($input, $key));
        }

        return $output;
    }

    /**
     * @return bool
     */
    private function throwIfNotExcepted()
    {
        if (!($this->internal instanceof HttpFoundationRequest || $this->internal instanceof Request)) {
            throw new \InvalidArgumentException('Not supported request instance');
        }
    }

    /**
     * Creates server request from globals.
     *
     * @throws \InvalidArgumentException
     *
     * @return HttpFoundationRequest
     */
    private function createFromServerGlobals()
    {
        return HttpFoundationRequest::createFromGlobals();
    }
}
