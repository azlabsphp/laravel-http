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
        return response()->json(
            $data,
            $status,
            $headers,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Convert an authorization exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception|null  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorized($request, \Exception $exception = null)
    {
        $message = $request->method() . ' ' . $request->path() . '  Unauthorized access.' . (isset($exception) ? ' [ERROR] : ' . $exception->getMessage() : '');
        return response($message, 401);
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
    public function download($pathToFile, $downloadedFileName = null, $headers = array(), $deleteAfterSend = false)
    {
        $result = response()->download($pathToFile, $downloadedFileName, $headers);
        return $result->deleteFileAfterSend($deleteAfterSend);
    }

    /**
     * Wrapper arround Illuminate streamDownload method of the [\Illuminate\Contracts\Routing\ResponseFactory::class]
     *
     * @param [type] $filename
     * @param \Closure $callback
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streamDownload($filename, \Closure $callback)
    {
        return response()->streamDownload($callback, $filename);
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
    public function loadFile($pathToFile, $headers = array())
    {
        return response()->file($pathToFile, $headers);
    }
}
