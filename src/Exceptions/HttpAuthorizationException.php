<?php

namespace Drewlabs\Packages\Http\Exceptions;

use Drewlabs\Packages\Http\ServerRequest;

/**
 * 
 * @package Drewlabs\Packages\Http\Exceptions
 */
class HttpAuthorizationException extends HttpException
{

    public function __construct($request, $message = '')
    {        
        /**
        * @var ServerRequest
        */
       $request = $request ? new ServerRequest($request) : $request;
       $message = $request ? sprintf("Not authorized request for : %s", $request->path()) : 'Unauthorized request';
       $headers = $request ? $request->getHeaders() : [];
       parent::__construct(401, $message, null, $headers);
    }
}