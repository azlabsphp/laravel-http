<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Laravel\Http\Middleware;

use Drewlabs\Cors\CorsInterface;
use Drewlabs\Http\Factory\Psr\PsrRequestFactoryInterface;
use Drewlabs\Http\Factory\Psr\PsrResponseFactoryInterface;
use Drewlabs\Laravel\Http\Factory\LaravelResponseFactory;
use Drewlabs\Laravel\Http\Factory\PsrRequestFactory;
use Drewlabs\Laravel\Http\Factory\PsrResponseFactory;
use Illuminate\Http\Response;
use Nyholm\Psr7\Factory\Psr17Factory;

class_exists(Psr17Factory::class);

class Cors
{
    /** @var CorsInterface */
    private $cors;

    /** @var PsrRequestFactoryInterface */
    private $psrRequestFactory;

    /** @var PsrResponseFactoryInterface */
    private $psrResponseFactory;

    /**
     * Creates class instance.
     */
    public function __construct(
        CorsInterface $cors,
        ?PsrRequestFactoryInterface $psrRequestFactory = null,
        ?PsrResponseFactoryInterface $psrResponseFactory = null,
    ) {
        $this->cors = $cors;

        // Set the PSR-7 Request and PSR-7 Response factories
        $psr17Factory = new Psr17Factory();
        $this->psrRequestFactory = $psrRequestFactory ?? new PsrRequestFactory($psr17Factory, $psr17Factory, $psr17Factory);
        $this->psrResponseFactory = $psrResponseFactory ?? new PsrResponseFactory($psr17Factory, $psr17Factory);
    }

    /**
     * Passerelle d'écriture des en-têtes HTTP pour les accès CORS.
     *
     * @template TResponse
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return TResponse
     */
    public function handle($request, \Closure $next)
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
