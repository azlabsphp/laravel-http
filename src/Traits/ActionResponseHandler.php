<?php

namespace Drewlabs\Packages\Http\Traits;

trait ActionResponseHandler
{

    /**
     * Controllers Http response formatter
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
