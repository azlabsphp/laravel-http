<?php

namespace Drewlabs\Packages\Http\Traits;

trait ResponseHandler
{


    /**
     * HTTP Status code
     *
     * @var int
     */
    private $status_code;

    /**
     * HTTP Headers
     *
     * @var array
     */
    private $headers = [];

    /**
     * Controllers Http response formatter
     * 
     * @param mixed $data
     * @param int $response_code
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $status, $headers = array())
    {
        if (function_exists('response')) {
            return call_user_func('response')->json(
                $data,
                $status,
                $headers,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );
        }
        throw new \RuntimeException("Error Processing Request - Lumen or Laravel framework is required to work with the this class", 500);
    }

    /**
     * Add status code to the HTTP response
     *
     * @param integer $code
     * @return static
     */
    public function withStatus(int $code = 200)
    {
        $this->status_code = $code;
        return $this;
    }

    /**
     * Add headers to the HTTP response
     *
     * @param array $headers
     * @return static
     */
    public function withHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }
}
