<?php

namespace Drewlabs\Packages\Http\Contracts;

use Drewlabs\Contracts\Http\ResponseHandler as ContractsResponseHandler;
use Drewlabs\Contracts\Http\UnAuthorizedResponseHandler;
use Drewlabs\Contracts\Http\BinaryResponseHandler;


interface ResponseHandler extends
    BinaryResponseHandler,
    ContractsResponseHandler,
    UnAuthorizedResponseHandler
{
}
