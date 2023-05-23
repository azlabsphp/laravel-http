<?php

namespace Drewlabs\Packages\Http\Middleware;

use Closure;
use Drewlabs\Cors\CorsInterface;
use Drewlabs\Http\Factory\Psr\PsrRequestFactoryInterface;
use Drewlabs\Http\Factory\Psr\PsrResponseFactoryInterface;
use Drewlabs\Packages\Http\Factory\LaravelResponseFactory;
use Drewlabs\Packages\Http\Factory\PsrRequestFactory;
use Drewlabs\Packages\Http\Factory\PsrResponseFactory;
use Illuminate\Http\Response;
use Nyholm\Psr7\Factory\Psr17Factory;

class_exists(Psr17Factory::class);

class Cors
{

    /**
     * @var CorsInterface
     */
    private $cors;

    /**
     * @var PsrRequestFactoryInterface
     */
    private $psrRequestFactory;

    /**
     * @var PsrResponseFactoryInterface
     */
    private $psrResponseFactory;

    /**
     * Creates class instance
     * 
     * @param CorsInterface $cors 
     * @param PsrRequestFactoryInterface $psrRequestFactory 
     * @param PsrResponseFactoryInterface $psrResponseFactory 
     */
    public function __construct(
        CorsInterface $cors,
        PsrRequestFactoryInterface $psrRequestFactory = null,
        PsrResponseFactoryInterface $psrResponseFactory = null,
    ) {
        $this->cors = $cors;

        // Set the PSR-7 Request and PSR-7 Response factories
        $psr17Factory = new Psr17Factory;
        $this->psrRequestFactory  = $psrRequestFactory ?? new PsrRequestFactory($psr17Factory, $psr17Factory, $psr17Factory);
        $this->psrResponseFactory = $psrResponseFactory ?? new PsrResponseFactory($psr17Factory, $psr17Factory);
    }

    /**
     * Passerelle d'écriture des en-têtes HTTP pour les accès CORS
     *
     * @template TResponse
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * 
     * @return TResponse
     */
    public function handle($request, Closure $next)
    {
        // Creates PSR-7 request from laravel request
        $psr7Request = $this->psrRequestFactory->create($request);

        // Checks if the request is a core request
        if (!$this->cors->isCorsRequest($psr7Request)) {
            return $next($request);
        }
        
        // Create response object for the HTTP request
        $response = $this->cors->isPreflightRequest($psr7Request) ? $this->psrResponseFactory->create(new Response()) : $this->psrResponseFactory->create($next($request));

        // Convert Psr7 response to Laravel response instance
        return (new LaravelResponseFactory())->create($this->cors->handleRequest($psr7Request, $response));
    }
}
