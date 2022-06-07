<?php

namespace Drewlabs\Packages\Http\Middleware;

/**
 * 
 * @package Drewlabs\Packages\Http\Middleware
 */
class EmptyStringToNull extends TransformRequest
{
    protected function transform($key, $value)
    {
        return is_string($value) && $value === '' ? null : $value;
    }
}
