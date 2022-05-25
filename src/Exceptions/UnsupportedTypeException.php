<?php

namespace Drewlabs\Packages\Http\Exceptions;

use Exception;

/**
 * 
 * @package Drewlabs\Packages\Http\Exceptions
 */
class UnsupportedTypeException extends Exception
{

    /**
     * Create ans instance of {@see UnsupportedTypeException} from a response instance
     * 
     * @param mixed $response 
     * @return UnsupportedTypeException 
     */
    public static function forResponse($response)
    {
        if (!is_object($response)) {
            return new self('Response type is not supported!');
        } else {
            return new self('Response of type ' . get_class($response) . ' is not supported!');
        }
    }

    /**
     * Create ans instance of {@see UnsupportedTypeException} from a request instance
     * 
     * @param mixed $request 
     * @return UnsupportedTypeException 
     */
    public static function forRequest($request)
    {
        if (!is_object($request)) {
            return new self('Request type is not supported!');
        } else {
            return new self('Request of type ' . get_class($request) . ' is not supported!');
        }
    }
}
