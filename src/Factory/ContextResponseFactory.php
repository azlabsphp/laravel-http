<?php

namespace Drewlabs\Packages\Http\Factory;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

trait ContextResponseFactory
{

    /**
     * @var \Closure($content = '', $status = 200, array $headers = []): \Symfony\Component\HttpFoundation\Response
     */
    private $responseFactory;

    /**
     * Creates response instance from provided parameters
     * 
     * @param string $content 
     * @param int $status 
     * @param array $headers 
     * @return HttpFoundationResponse 
     */
    private function createResponse($content = '', $status = 200, array $headers = [])
    {
        return ($this->responseFactory)($content, $status, $headers);
    }

    /**
     * Creates a default response factory class
     * 
     * @return ContextResponseFactory 
     */
    public static function useDefault()
    {
        return new self(function ($data = null, $status = 200, $headers = []) {
            return new Response($data, $status, $headers);
        });
    }
}
