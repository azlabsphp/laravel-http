<?php

namespace Drewlabs\Packages\Http\Contracts;

use Drewlabs\Contracts\Http\ResponseHandler as BaseResponseHandler;
use Drewlabs\Contracts\Http\UnAuthorizedResponseHandler;
use Drewlabs\Contracts\Http\BinaryResponseHandler;


interface ResponseHandler extends
    BinaryResponseHandler,
    BaseResponseHandler,
    UnAuthorizedResponseHandler
{
}
