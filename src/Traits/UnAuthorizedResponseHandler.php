<?php

namespace Drewlabs\Packages\Http\Traits;

trait UnAuthorizedResponseHandler
{
    /**
     * Convert an authorization exception into a response.
     *
     * @param  Request  $request
     * @param  \Exception|null  $exception
     * @return Response
     */
    public function unauthorized($request, \Exception $exception = null)
    {
        if (function_exists('response')) {
            $message = $request->method() . ' ' . $request->path() . '  Unauthorized access.' . (isset($exception) ? ' [ERROR] : ' . $exception->getMessage() : '');
            return call_user_func_array('response', array($message, 401));
        }
        throw new \RuntimeException("Error Processing Request - Lumen or Laravel framework is required to work with the this class", 500);
    }
}
