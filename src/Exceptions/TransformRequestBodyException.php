<?php

namespace Drewlabs\Packages\Http\Exceptions;

use Illuminate\Http\Request;

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
    public function __construct(\Illuminate\Http\Request $request = null, $message = 'Bad transform request configuration error', $code = 500)
    {
        if (isset($request)) {
            $message = "Request path : /" . $request->path() . " Error : $message";
        }
        parent::__construct($message, $code);
    }
}
