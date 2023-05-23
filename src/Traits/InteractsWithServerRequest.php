<?php

namespace Drewlabs\Packages\Http\Traits;

use Drewlabs\Core\Helpers\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

/**
 * @mixin \Drewlabs\Packages\Http\Traits\HasAuthenticatable
 * @mixin \Drewlabs\Packages\Http\Traits\ContainerAware
 */
trait InteractsWithServerRequest
{
    /**
     * @var Request|HttpFoundationRequest
     */
    private $request;
    
    /**
     * Context request setter and getter
     * 
     * @param Request|HttpFoundationRequest|null $request 
     * 
     * @return Request|HttpFoundationRequest 
     */
    public function request($request = null)
    {
        if (null !== $request) {
            $this->request = $request;
        }
        return $this->request;
    }

    /**
     * Query `bearer token` header value from context request
     * 
     * @return null|string 
     */
    public function bearerToken()
    {
        if (null === $this->request) {
            return null;
        }
        if (null === ($header = $this->request->headers->get('authorization'))) {
            return null;
        }
        if (!Str::startsWith(strtolower($header), 'bearer')) {
            return null;
        }
        return trim(str_ireplace('bearer', '', $header));
    }

    /**
     * Query `basic auth` header value from context request instance.
     * 
     * It returns a tuple consisting of [$username, $password]
     * 
     * @return array<string>
     */
    public function basicAuth()
    {
        if (null === $this->request) {
            return [];
        }
        if (null === ($header = $this->request->headers->get('authorization'))) {
            return [];
        }
        if (!Str::startsWith(strtolower($header), 'basic')) {
            return [];
        }
        $base64 = trim(str_ireplace('basic', '', $header));

        return explode(':', \base64_decode($base64));
    }
}
