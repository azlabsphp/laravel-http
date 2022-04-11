<?php

namespace Drewlabs\Packages\Http\Traits;

use Drewlabs\Packages\Http\Exceptions\UnsupportedTypeException;

trait HttpMessageTrait
{
    /**
     * 
     * @param string $header 
     * @return bool 
     * @throws UnsupportedTypeException 
     */
    public function hasHeader(string $header)
    {
        return null !== $this->getHeader($header, null);
    }

    /**
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
        throw UnsupportedTypeException::forRequest($this->internal);
    }

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
        throw UnsupportedTypeException::forRequest($this->internal);
    }
}
