<?php

namespace Drewlabs\Packages\Http\Contracts;

use Drewlabs\Contracts\Http\ResponseHandler;
use Drewlabs\Contracts\Http\UnAuthorizedResponseHandler;
use Drewlabs\Contracts\Http\BinaryResponseHandler;

/**
 *  @package Drewlabs\Packages\Http\Contracts 
 * */
interface IActionResponseHandler extends
    BinaryResponseHandler,
    ResponseHandler,
    UnAuthorizedResponseHandler
{
}
