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

trait HttpMessageTrait
{
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
        return $this->internal->headers->all();
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
        return $this->internal->headers->get($name, $default);
    }

    /**
     * Set the HTTP Message header value.
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setHeader(string $header, $value)
    {
        $this->internal->headers->set($header, $value, true);

        return $this;
    }
}
