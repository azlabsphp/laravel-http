<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Contracts\Http\UnAuthorizedResponseHandler;
use Drewlabs\Packages\Http\Traits\UnAuthorizedResponseHandler as TraitsUnAuthorizedResponseHandler;

/** @package Drewlabs\Packages\Http */
class UnAuthorizedResponse implements UnAuthorizedResponseHandler
{
    use TraitsUnAuthorizedResponseHandler;
}