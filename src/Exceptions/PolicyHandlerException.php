<?php //

namespace Drewlabs\Packages\Http\Exceptions;

use Illuminate\Http\Request;

/**
 * @deprecated v2.3.x
 * 
 * @package Drewlabs\Packages\Http\Exceptions
 */
class PolicyHandlerException extends \RuntimeException
{
    /**
     * Creates an instance of {@see PolicyHandlerException}
     * 
     * @param Request $request 
     * @param string $message 
     * @param int $code 
     */
    public function __construct(\Illuminate\Http\Request $request, $message = 'Bad policy handler confuguration error', $code = 500)
    {
        $msg = "Request path : /" . $request->path() . " Error : $message";
        parent::__construct($msg, $code);
    }
}
