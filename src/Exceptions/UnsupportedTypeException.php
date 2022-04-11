<?php

namespace Drewlabs\Packages\Http\Exceptions;

use Exception;

class UnsupportedTypeException extends Exception
{

    public static function forResponse($response)
    {
        if (!is_object($response)) {
            return new self('Response type is not supported!');
        } else {
            return new self('Response of type ' . get_class($response) . ' is not supported!');
        }
    }

    public static function forRequest($request)
    {
        if (!is_object($request)) {
            return new self('Request type is not supported!');
        } else {
            return new self('Request of type ' . get_class($request) . ' is not supported!');
        }
    }
}
