<?php

namespace Drewlabs\Packages\Http\Traits;

trait HttpMessageTrait
{

    /**
     * Checks if the HTTP message has a given header
     * 
     * @param string $header
     * 
     * @return bool 
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
        return $this->internal->headers->all();
    }

    /**
     * Return the HTTP Message header value or $default if the header is not present
     * 
     * @template TResult
     * 
     * @param string $name 
     * @param TResult $default
     * 
     * @return string|TResult
     */
    public function getHeader(string $name, $default = null)
    {
        return $this->internal->headers->get($name, $default);
    }

    /**
     * Set the HTTP Message header value
     * 
     * @param string $header 
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
