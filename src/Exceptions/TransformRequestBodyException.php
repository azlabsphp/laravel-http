<?php

namespace Drewlabs\Packages\Http\Exceptions;

use Illuminate\Http\Request;

/**
 * @deprecated v2.0.x
 * 
 * @package Drewlabs\Packages\Http\Exceptions
 */
class TransformRequestBodyException extends \RuntimeException
{
    /**
     * Creates an instance of {@see TransformRequestBodyException} class
     * 
     * @param Request|null $request 
     * @param string $message 
     * @param int $code 
     * @return self 
     */
    public function __construct($request = null, $message = 'Bad transform request configuration error', $code = 500)
    {
        $message = "Request path : /" . ($request ? $request->path() : '') . " Error : $message";
        parent::__construct($message, $code);
    }
}
