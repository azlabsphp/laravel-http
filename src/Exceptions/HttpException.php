<?php

namespace Drewlabs\Packages\Http\Exceptions;

use Exception;
use Throwable;

/**
 * 
 * @package Drewlabs\Packages\Http\Exceptions
 */
class HttpException extends Exception
{
    /**
     * 
     * @var array
     */
    private $headers;

    /**
     * 
     * @var int
     */
    private $statusCode;

    /**
     * Creates an instance of HttpException
     * 
     * @param int|string $statusCode 
     * @param string $message 
     * @param Throwable|null $previous 
     * @param array $headers 
     * @param int $code 
     * @return void 
     */
    public function __construct($statusCode, $message = '', Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = (int)$statusCode;
        $this->headers = $headers;
    }

    /**
     * Returns the HTTP headers
     * 
     * @return array 
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns the HTTP status code
     * 
     * @return int 
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
