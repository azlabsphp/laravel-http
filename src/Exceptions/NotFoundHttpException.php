<?php

namespace Drewlabs\Packages\Http\Exceptions;

use Exception;
use Illuminate\Http\Request;

class NotFoundHttpException extends Exception
{
    /**
     * Creates an instance of {@see TransformRequestBodyException} class
     * 
     * @param Request|null $request 
     * @param string $message 
     * @param int $code 
     * @return self 
     */
    public function __construct(Request $request = null)
    {
        if (isset($request)) {
            $message = sprintf("Missing resource, request path: %s", $request->path());
        }
        parent::__construct($message);
    }
}
