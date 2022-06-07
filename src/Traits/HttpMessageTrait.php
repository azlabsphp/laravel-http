<?php

namespace Drewlabs\Packages\Http\Traits;

use Drewlabs\Packages\Http\Exceptions\NotSupportedMessageException;

trait HttpMessageTrait
{
    /**
     * Checks if the HTTP message has a given header
     * 
     * @param string $header 
     * @return bool 
     * @throws NotSupportedMessageException 
     */
    public function hasHeader(string $header)
    {
        return null !== $this->getHeader($header, null);
    }

    /**
     * Returns the list of the message headers
     * 
     * @return string[]|array 
     */
    public function  getHeaders()
    {
        if ($this->isPsr7()) {
            return $this->internal->getHeaders();
        }
        return $this->internal->headers->all();
    }

    /**
     * Return the HTTP Message header value or $default if
     * the header is not present
     * 
     * @param string $name 
     * @param mixed $default 
     * @return string 
     */
    public function getHeader(string $name, $default = null)
    {
        if ($this->isPsr7()) {
            $headers = $this->internal->getHeader($name);
            return array_pop($headers) ?? $default;
        }
        if ($this->isSymfony()) {
            return $this->internal->headers->get($name, $default);
        }
        throw NotSupportedMessageException::forRequest($this->internal);
    }

    /**
     * Set the HTTP Message header value
     * 
     * @param string $header 
     * @param mixed $value 
     * @return $this 
     * @throws NotSupportedMessageException 
     */
    public function setHeader(string $header, $value)
    {
        if ($this->isSymfony()) {
            $this->internal->headers->set($header, $value, true);
            return $this;
        }
        if ($this->isPsr7()) {
            $this->internal = $this->internal->withHeader($header, $value);
            return $this;
        }
        throw NotSupportedMessageException::forRequest($this->internal);
    }
}
