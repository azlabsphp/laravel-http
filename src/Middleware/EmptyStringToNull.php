<?php

namespace Drewlabs\Packages\Http\Middleware;

class EmptyStringToNull extends LaravelTransformRequest
{
    /**
     * @inheritDoc
     */
    protected function transform($key, $value)
    {
        return is_string($value) && $value === '' ? null : $value;
    }
}
