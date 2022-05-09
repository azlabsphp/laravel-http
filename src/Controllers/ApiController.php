<?php

namespace Drewlabs\Packages\Http\Controllers;

use AutorizeRequest;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Contracts\Container\Container as Application;
use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Drewlabs\Packages\Http\Traits\LaravelOrLumenFrameworksApiController;
use Drewlabs\Contracts\Validator\Validator as ValidatorContract;

/**
 * @deprecated v2.x
 * 
 * @package Drewlabs\Packages\Http\Controllers
 */
abstract class ApiController
{

    use AutorizeRequest;

    /**
     *
     * @var Application
     */
    protected $app;

    /**
     *
     * @var IActionResponseHandler
     */
    protected $responseHandler;

    /**
     * Injected instance of drewlabs validator class
     *
     * @var ValidatorContract
     */
    protected $viewModelValidator;

    /**
     * Base controller object initialiser
     *
     * @param Application $app
     */
    public function __construct()
    {
        $this->app = self::createResolver()();
        $this->responseHandler = $this->app->make(IActionResponseHandler::class);
        $this->viewModelValidator = $this->app->make(ValidatorContract::class);
    }

    /**
     * Controllers Http response formatter
     * @param mixed $data
     * @param int $response_code
     * @param array $headers
     * @return Response
     * 
     */
    protected function respond($data, $status, $headers = array())
    {
        return $this->responseHandler->respond($data, $status, $headers);
    }
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param mixed $data
     * @param array|null $errors
     * @param bool $success
     * @return Response
     * 
     */
    protected function respondOk($data, array $errors = null, $success = true)
    {
        return $this->responseHandler->ok($data, $errors, $success);
    }
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param \Exception $e
     * @param array|null $errors
     * @return Response
     * 
     */
    protected function respondError(\Exception $e, array $errors = null)
    {
        return $this->responseHandler->error($e, $errors);
    }
    /**
     * Method for converting bad request to a json formatted response
     *
     * @param array $errors
     * @return Response
     * 
     */
    protected function respondBadRequest(array $errors)
    {
        return $this->responseHandler->badRequest($errors);
    }

    /**
     * Convert an authorization exception into a response.
     *
     * @param  Request  $request
     * @param  \Exception|null  $exception
     * @return Response
     * 
     */
    protected function unauthorized($request, \Exception $exception = null)
    {
        return $this->responseHandler->unauthorized($request, $exception);
    }

    // Provides functionnalities for managing file download

    /**
     * A wrapper method arround illuminate response()->download(...params) method
     *
     * @param string $path
     * @param string $name
     * @param array $headers
     * @param bool $deleteAfterSend
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * 
     */
    protected function download($path, $name = null, $headers = [], $deleteAfterSend = false)
    {
        return $this->responseHandler->download($path, $name, $headers, $deleteAfterSend);
    }

    /**
     * Wrapper arround Illuminate streamDownload method of the [\Illuminate\Contracts\Routing\ResponseFactory::class]
     *
     * @param [type] $filename
     * @param \Closure $callback
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * 
     */
    protected function streamDownload($filename, \Closure $callback)
    {
        return $this->responseHandler->stream($filename, $callback);
    }
    /**
     * This method is a wrapper arround of the [\Illuminate\Contracts\Routing\ResponseFactory::class] [[file]]
     * method that may be used to display a file, such as an image or PDF, directly in the user's browser
     * instead of initiating a download.
     *
     * @param string $path
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * 
     */
    protected function loadFile($path, $headers = [])
    {
        return $this->responseHandler->file($path, $headers);
    }
}
