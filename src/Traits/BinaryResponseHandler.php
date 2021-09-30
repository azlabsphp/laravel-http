<?php

namespace Drewlabs\Packages\Http\Traits;

trait BinaryResponseHandler
{
    /**
     * {@inheritDoc}
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($pathToFile, $downloadedFileName = null, $headers = [], $deleteAfterSend = false)
    {
        if (function_exists('response')) {
            $result = call_user_func('response')->download(
                $pathToFile,
                $downloadedFileName,
                $headers
            );
            return $deleteAfterSend ? $result->deleteFileAfterSend(true) : $result;
        }
        throw new \RuntimeException("Error Processing Request - Lumen or Laravel framework is required to work with the this class", 500);
    }

    /**
     * {@inheritDoc}
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function stream($filename, \Closure $callback)
    {
        if (function_exists('response')) {
            return call_user_func('response')->stream($callback, $filename);
        }
        throw new \RuntimeException("Error Processing Request - Lumen or Laravel framework is required to work with the this class", 500);
    }
    /**
     * {@inheritDoc}
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function file($path, $headers = array())
    {
        if (function_exists('response')) {
            return call_user_func('response')->file($path, $headers);
        }
        throw new \RuntimeException("Error Processing Request - Lumen or Laravel framework is required to work with the this class", 500);
    }
}
