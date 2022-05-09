<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Contracts\Http\UnAuthorizedResponseHandler as UnAuthorizedResponseHandlerInterface;
use Drewlabs\Packages\Http\Traits\UnAuthorizedResponseHandler;

/** @package Drewlabs\Packages\Http */
class UnAuthorizedResponse implements UnAuthorizedResponseHandlerInterface
{
    use UnAuthorizedResponseHandler;
}