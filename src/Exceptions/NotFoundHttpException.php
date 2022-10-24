<?php

namespace Drewlabs\Packages\Http\Exceptions;

use Drewlabs\Packages\Http\ServerRequest;

class NotFoundHttpException extends HttpException
{
    /**
     * Creates an instance of {@see TransformRequestBodyException} class
     * 
     * @param mixed $request 
     * @param string $message 
     * @param int $code 
     * @return self 
     */
    public function __construct($request = null)
    {
        /**
         * @var ServerRequest
         */
        $request = $request ? new ServerRequest($request) : $request;
        $message = $request ? sprintf("No handler for request path: %s", $request->path()) : 'Not found';
        $headers = $request ? $request->getHeaders() : [];
        parent::__construct(404, $message, null, $headers);
    }
}
