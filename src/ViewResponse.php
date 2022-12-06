<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Contracts\Http\ViewResponseHandler as HttpViewResponseHandler;
use Drewlabs\Packages\Http\Traits\ContainerAware;

class ViewResponse implements HttpViewResponseHandler
{
    use ContainerAware;

    public function view(string $path, array $data = [])
    {
        return self::createResolver('view')()->make($path, $data);
    }
}
