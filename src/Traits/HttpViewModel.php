<?php

namespace Drewlabs\Packages\Http\Traits;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

trait HttpViewModel
{
    use InteractsWithServerRequest;
    use ContainerAware;
    use AutorizeRequest;
    use HasAuthenticatable;

    /**
     * Set curren instance attributes from framework request attributes
     * 
     * @param HttpFoundationRequest|Request $request
     * 
     * @return void 
     */
    private function buildInstanceFromRequestAttibutes(HttpFoundationRequest $request = null)
    {
        try {
            // Making the class injectable into controller actions
            // by resolving the current request from the service container
            $this->request = $request ?? Container::getInstance()->make('request');

            // We set the current body, query and files attributes from the request context
            $this->fromContextRequest($this->request);

            // We set the current instance user resolver property
            $this->setUserResolver($this->request instanceof Request ? $this->request->getUserResolver() : function() {
                return null;
            });
        } catch (\Throwable $e) {
            // We catch the exception for it to not propagate
        }
    }
}
