<?php

namespace Drewlabs\Packages\Http\Middleware\Cors\Contracts;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface CorsServiceInterface
{
    /**
     * Returns whether or not the request is a CORS request.
     *
     * @param Request|ServerRequestInterface $request
     *
     * @return bool
     */
    public function isCorsRequest($request);
    /**
     * Returns whether or not the request is a preflight request.
     *
     * @param Request|ServerRequestInterface $request
     *
     * @return bool
     */
    public function isPreflightRequest($request);

    /**
     * Handles the actual request.
     *
     * @param Request|ServerRequestInterface  $request
     * @param Response|ResponseInterface $response
     *
     * @return Response|ResponseInterface
     */
    public function handleRequest($request, $response);
}
