<?php

namespace Drewlabs\Packages\Http\Exceptions;

use Drewlabs\Packages\Http\ServerRequest;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;

/**
 * 
 * @package Drewlabs\Packages\Http\Exceptions
 */
class RequestValidationException extends HttpException
{
    /**
     * 
     * @param mixed $request 
     * @param string $message 
     * @param int $code 
     * @return void 
     * @throws SuspiciousOperationException 
     * @throws ConflictingHeadersException 
     * @throws NotSupportedMessageException 
     */
    public function __construct($request = null, $message = 'Bad validation configuration error', $code = 500)
    {
        /**
         * @var ServerRequest
         */
        $request = $request ? new ServerRequest($request) : $request;
        $message = $request ? "Request path : /" . $request->path() . " Error : $message" : $message;
        $headers = $request ? $request->getHeaders() : [];
        parent::__construct(422, $message, null, $headers, $code);
    }
}
