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

namespace Drewlabs\Laravel\Http\Traits;

use Closure;
use Error;
use BadMethodCallException;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

trait HttpMessageTrait
{
    /**
     * @var Request|HttpFoundationRequest|HttpFoundationResponse|HttpResponse
     */
    private $message;

    /**
     * Checks if the HTTP message has a given header.
     *
     * @return bool
     */
    public function hasHeader(string $header)
    {
        return null !== $this->getHeader($header, null);
    }

    /**
     * Returns the list of the message headers.
     *
     * @return string[]|array
     */
    public function getHeaders()
    {
        return $this->message->headers->all();
    }

    /**
     * Return the HTTP Message header value or $default if the header is not present.
     *
     * @template TResult
     *
     * @param TResult $default
     *
     * @return string|TResult
     */
    public function getHeader(string $name, $default = null)
    {
        return $this->message->headers->get($name, $default);
    }

    /**
     * Set the HTTP Message header value.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function setHeader(string $header, $value)
    {
        $this->message->headers->set($header, $value, true);

        return $this;
    }

    /**
     * Provide a proxy to the message instance
     * 
     * @param mixed $object 
     * @param mixed $method 
     * @param array $args 
     * @param Closure|null $default 
     * @return mixed 
     * @throws Error 
     * @throws BadMethodCallException 
     */
    public function proxy($object, $method, $args = [], ?\Closure $default = null)
    {
        try {
            // Call the method on the provided object
            return $object->{$method}(...$args);
        } catch (\Error|\BadMethodCallException $e) {
            // Call the default method if the specified method does not exits
            if ((null !== $default) && \is_callable($default)) {
                return $default(...$args);
            }
            $pattern = '~^Call to undefined method (?P<class>[^:]+)::(?P<method>[^\(]+)\(\)$~';
            if (!preg_match($pattern, $e->getMessage(), $matches)) {
                throw $e;
            }
            if ($matches['class'] !== \get_class($object) || $matches['method'] !== $method) {
                throw $e;
            }
            throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $method));
        }
    }
}
