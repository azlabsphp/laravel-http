<?php

namespace Drewlabs\Packages\Http\Contracts;

interface IActionResponseHandler
{
    /**
     * Controllers Http response formatter
     * @param mixed $data
     * @param int $response_code
     * @param array $headers
     * @return Response
     */
    public function respond($data, $status, $headers = array());
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param mixed $data
     * @param array|null $errors
     * @param bool $success
     * @return Response
     */
    public function respondOk($data, array $errors = null, $success = true);
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param \Exception $e
     * @param array|null $errors
     * @return Response
     */
    public function respondError(\Exception $e, array $errors = null);
    /**
     * Method for converting bad request to a json formatted response
     *
     * @param array $errors
     * @return Response
     */
    public function respondBadRequest(array $errors);

    /**
     * Return an HTTP JSON response with status >=200 AND <=204
     *
     * @param mixed $data
     * @param array|null $errors
     * @param bool $success
     * @return Response
     */
    public function ok($data, array $errors = null, $success = true);

    /**
     * Return a Server Error HTTP response  with status 500
     *
     * @param \Exception $e
     * @param array|null $errors
     * @return Response
     */
    public function error(\Exception $e, array $errors = null);

    /**
     * Return an HTTP Bad Request response  with status >=400 or <=403
     *
     * @param array $errors
     * @return Response
     */
    public function badRequest(array $errors);

    /**
     * Add status code to the HTTP response
     *
     * @param integer $code
     * @return static
     */
    public function withStatus(int $code);    
    
    /**
    * Add headers to the HTTP response
    *
    * @param array $headers
    * @return static
    */
   public function withHeaders(array $headers);

    /**
     * Convert an authorization exception into a response.
     *
     * @param  Request  $request
     * @param  \Exception|null  $exception
     * @return Response
     */
    public function unauthorized($request, \Exception $exception = null);

    // Provides functionnalities for managing file download

    /**
     * A wrapper method arround illuminate response()->download(...params) method
     *
     * @param string $pathToFile
     * @param string $downloadedFileName
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($pathToFile, $downloadedFileName = null, $headers = array(), $deleteAfterSend = false);

    /**
     * Wrapper arround Illuminate streamDownload method of the [\Illuminate\Contracts\Routing\ResponseFactory::class]
     *
     * @param [type] $filename
     * @param \Closure $callback
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streamDownload($filename, \Closure $callback);
    /**
     * This method is a wrapper arround of the [\Illuminate\Contracts\Routing\ResponseFactory::class] [[file]]
     * method that may be used to display a file, such as an image or PDF, directly in the user's browser
     * instead of initiating a download.
     *
     * @param string $path
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function file($path, $headers = array());
}
