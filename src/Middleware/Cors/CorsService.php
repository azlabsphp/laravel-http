<?php

namespace Drewlabs\Packages\Http\Middleware\Cors;

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Packages\Http\Middleware\Cors\Contracts\CorsServiceInterface;
use Drewlabs\Packages\Http\Facades\HttpRequest;
use Drewlabs\Packages\Http\Facades\HttpResponse;
use Drewlabs\Packages\Http\Response;

final class CorsService implements CorsServiceInterface
{
    /**
     * List of allowed hosts
     *
     * @var array
     */
    private $allowed_hosts = ['*'];

    /**
     * Access control max age header value
     *
     * @var integer
     */
    private $max_age = 0;

    private $allowed_methods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'OPTIONS',
    ];

    private $allowed_headers = [
        'X-Requested-With',
        'Content-Type',
        'Accept',
        'Origin',
        'Authorization',
        'Application',
        'Cache-Control',
    ];

    /**
     * 
     * @var true
     */
    private $allowed_credentials = true;

    /**
     * 
     * @var array
     */
    private $exposed_headers = [];
    /**
     * Current request Request-Headers entries
     *
     * @var string
     */
    private $accessControlRequestHeadersHeader = 'Access-Control-Request-Headers';
    /**
     * Current request Request-Methods entries
     *
     * @var string
     */
    private $accessControlRequestMethodHeader = 'Access-Control-Request-Method';
    /**
     * Max age of the request headers
     *
     * @var string
     */
    private $accessControlMaxAgeHeader = 'Access-Control-Max-Age';
    /**
     * Entry for the Allowed methods to be set on the request
     *
     * @var string
     */
    private $accessControlAllowedMethodHeader = 'Access-Control-Allow-Methods';
    /**
     *
     * @var string
     */
    private $accessControlAllowedCredentialsHeader = 'Access-Control-Allow-Credentials';
    /**
     * Entry for the Allowed header to be set on the request
     *
     * @var string
     */
    private $accessControlAllowedHeadersHeader = 'Access-Control-Allow-Headers';
    /**
     * Entry for the exposed headers to be set on the request
     *
     * @var string
     */
    private $accessControlExposedHeadersHeader = 'Access-Control-Expose-Headers';
    /**
     * Entry for the allowed origins to be set on the request
     *
     * @var string
     */
    private $accessControlAllowedOriginHeader = 'Access-Control-Allow-Origin';


    private $fillables = [
        'allowed_hosts',
        'max_age',
        'allowed_headers',
        'allowed_credentials',
        'exposed_headers'
    ];

    /**
     * Object initializer
     *
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        $this->forceFill($config ?? []);
    }

    public function isCorsRequest($request)
    {
        return HttpRequest::hasHeader($request, 'Origin');
    }

    public function isPreflightRequest($request)
    {
        return $this->isCorsRequest($request) &&
            HttpRequest::isMethod($request, 'OPTIONS') &&
            HttpRequest::hasHeader($request, 'Access-Control-Request-Method');
    }

    /**
     *
     * @inheritDoc
     */
    public function handleRequest($request, $response)
    {
        if ($this->isPreflightRequest($request)) {
            return $this->handlePreflightRequest($request, $response);
        }
        // Do not set any headers if the origin is not allowed
        if ($this->matches($this->allowed_hosts, $request->headers->get('Origin'))) {
            return $this->handleNormalRequest($request, $response);
        }
        return $response;
    }

    public function handlePreflightRequest($request, $response)
    {
        // Do not set any headers if the origin is not allowed
        if ($this->matches($this->allowed_hosts, HttpRequest::getHeader($request, 'Origin'))) {
            // Set the allowed origin if it is a preflight request
            $response = $this->setAllowOriginHeaders($request, $response);
            // Set headers max age
            if ($this->max_age) {
                $response = HttpResponse::setHeader($response, $this->accessControlMaxAgeHeader, (string) $this->max_age);
            }
            // Set the allowed method headers
            $response = HttpResponse::setHeader(
                $response,
                $this->accessControlAllowedCredentialsHeader,
                $this->allowed_credentials ? 'true' : 'false'
            )->setHeader(
                $this->accessControlAllowedMethodHeader,
                in_array('*', $this->allowed_methods)
                    ? strtoupper($request->headers->get($this->accessControlRequestMethodHeader))
                    : implode(', ', $this->allowed_methods)
            )->setHeader(
                $this->accessControlAllowedHeadersHeader,
                in_array('*', $this->allowed_headers)
                    ? strtolower($request->headers->get($this->accessControlRequestHeadersHeader))
                    : implode(', ', $this->allowed_headers)
            )->unwrap();
        }
        return $response;
    }

    public function handleNormalRequest($request, $response)
    {
        $response = $this->setAllowOriginHeaders($request, $response);
        // Set Vary unless all origins are allowed
        if (!in_array('*', $this->allowed_hosts)) {
            $vary = HttpRequest::hasHeader($request, 'Vary') ? HttpRequest::getHeader($request, 'Vary') . ', Origin' : 'Origin';
            $response = HttpResponse::setHeader($response, 'Vary', $vary)->unwrap();
        }
        $response = HttpResponse::setHeader(
            $response,
            $this->accessControlAllowedCredentialsHeader,
            $this->allowed_credentials ? 'true' : 'false'
        );
        if (!empty($this->exposed_headers)) {
            $response = HttpResponse::setHeader(
                $response,
                $this->accessControlExposedHeadersHeader,
                implode(', ', $this->exposed_headers)
            );
        }
        return $response->unwrap();
    }

    private function forceFill(array $config)
    {
        foreach ($this->fillables as $key) {
            if (array_key_exists($key, $config) && !\is_null($config[$key] ?? null)) {
                if (is_array($first = $this->{$key}) && is_array($second = $config[$key])) {
                    $configs_ = Arr::unique(array_merge($first ?? [], $second ?? []));
                } else {
                    $configs_ = $config[$key];
                }
                //**Note*
                // By default if the allowed_hosts entry is empty we use ['*'] to allow
                // request from any origin
                if ($key === 'allowed_hosts') {
                    $configs_ = is_string($configs_) ? [$configs_] : (empty($configs_) ? ['*'] : $configs_);
                }
                $this->{$key} = $configs_;
            }
        }
    }

    /**
     * @param mixed  $request
     * @param mixed $response
     *
     * @return Response
     */
    private function setAllowOriginHeaders($request, $response)
    {
        $origin = HttpRequest::getHeader($request, 'Origin');
        if (in_array('*', $this->allowed_hosts)) {
            $response = HttpResponse::setHeader(
                $response,
                $this->accessControlAllowedOriginHeader,
                empty($origin) ? '*' : $origin
            );
        } else if ($this->matches($this->allowed_hosts, $origin)) {
            $response = HttpResponse::setHeader(
                $response,
                $this->accessControlAllowedOriginHeader,
                $origin
            );
        }
        return $response;
    }

    /**
     * Create a pattern for a wildcard, based on $this->matches() from Laravel
     *
     * @param string $pattern
     * @return string
     */
    private function convertWildcardToPattern($pattern)
    {
        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);

        return '#^' . $pattern . '\z#u';
    }

    private function matches($pattern, $value)
    {
        $patterns = Arr::wrap($pattern);

        $value = (string) $value;

        if (empty($patterns)) {
            return false;
        }
        foreach ($patterns as $pattern) {
            $pattern = (string) $pattern;
            if ($pattern == $value) {
                return true;
            }
            if (preg_match($this->convertWildcardToPattern($pattern), $value) === 1) {
                return true;
            }
        }

        return false;
    }
}
