<?php

namespace Drewlabs\Packages\Http\Controllers;

use Illuminate\Http\JsonResponse as Response;
use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Drewlabs\Packages\Http\Traits\LaravelOrLumenFrameworksApiController;
use Drewlabs\Contracts\Validator\Validator as ValidatorContract;
use Illuminate\Container\Container;

abstract class ApiController
{
    use LaravelOrLumenFrameworksApiController;

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
        $this->app = Container::getInstance();
        $this->responseHandler = $this->app->make(IActionResponseHandler::class);
        $this->viewModelValidator = $this->app->make(ValidatorContract::class);
    }

    /**
     * Checks if the current connected user has acces to admin ressources
     *
     * @return boolean
     */
    protected function hasAdminAcess()
    {
        return app(GateContract::class)->allows('is-admin');
    }

    /**
     * Controllers Http response formatter
     * @param mixed $data
     * @param int $response_code
     * @param array $headers
     * @return Response
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
     */
    protected function respondOk($data, array $errors = null, $success = true)
    {
        return $this->responseHandler->respondOk($data, $errors, $success);
    }
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param \Exception $e
     * @param array|null $errors
     * @return Response
     */
    protected function respondError(\Exception $e, array $errors = null)
    {
        return $this->responseHandler->respondError($e, $errors);
    }
    /**
     * Method for converting bad request to a json formatted response
     *
     * @param array $errors
     * @return Response
     */
    protected function respondBadRequest(array $errors)
    {
        return $this->responseHandler->respondBadRequest($errors);
    }

    /**
     * Convert an authorization exception into a response.
     *
     * @param  Request  $request
     * @param  \Exception|null  $exception
     * @return Response
     */
    protected function unauthorized($request, \Exception $exception = null)
    {
        if (function_exists('response')) {
            $message = $request->method() . ' ' . $request->path() . '  Unauthorized access.' . (isset($exception) ? ' [ERROR] : ' . $exception->getMessage() : '');
            return call_user_func_array('response', array($message, 401));
        }
        throw new \RuntimeException("Error Processing Request - Lumen or Laravel framework is required to work with the this class", 500);
    }

    // Provides functionnalities for managing file download

    /**
     * A wrapper method arround illuminate response()->download(...params) method
     *
     * @param string $pathToFile
     * @param string $downloadedFileName
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function download($pathToFile, $downloadedFileName = null, $headers = array(), $deleteAfterSend = false)
    {
        if (function_exists('response')) {
            $result = call_user_func('response')->download($pathToFile, $downloadedFileName, $headers);
            return $result->deleteFileAfterSend($deleteAfterSend);
        }
        throw new \RuntimeException("Error Processing Request - Lumen or Laravel framework is required to work with the this class", 500);
    }

    /**
     * Wrapper arround Illuminate streamDownload method of the [\Illuminate\Contracts\Routing\ResponseFactory::class]
     *
     * @param [type] $filename
     * @param \Closure $callback
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function streamDownload($filename, \Closure $callback)
    {
        if (function_exists('response')) {
            return call_user_func('response')->streamDownload($callback, $filename);
        }
        throw new \RuntimeException("Error Processing Request - Lumen or Laravel framework is required to work with the this class", 500);
    }
    /**
     * This method is a wrapper arround of the [\Illuminate\Contracts\Routing\ResponseFactory::class] [[file]]
     * method that may be used to display a file, such as an image or PDF, directly in the user's browser
     * instead of initiating a download.
     *
     * @param string $pathToFile
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function loadFile($pathToFile, $headers = array())
    {
        if (function_exists('response')) {
            return call_user_func('response')->file($pathToFile, $headers);
        }
        throw new \RuntimeException("Error Processing Request - Lumen or Laravel framework is required to work with the this class", 500);
    }
}
