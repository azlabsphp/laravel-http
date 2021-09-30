<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Packages\Http\Traits\BinaryResponseHandler as TraitsBinaryResponseHandler;
use Drewlabs\Contracts\Http\BinaryResponseHandler;

/** @package Drewlabs\Packages\Http */
class BinaryFileResponse implements BinaryResponseHandler
{
    use TraitsBinaryResponseHandler;
}
