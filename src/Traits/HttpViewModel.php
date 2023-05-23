<?php

namespace Drewlabs\Packages\Http\Traits;

use Illuminate\Container\Container;
use Illuminate\Http\Request;

trait HttpViewModel
{
    use InteractsWithServerRequest;
    use ContainerAware;
    use AuthorizeRequest;
    use HasAuthenticatable;

    /**
     * Set curren instance attributes from framework request attributes
     * 
     * @param Request $request
     * 
     * @return void 
     */
    private function buildInstanceFromRequestAttibutes($request = null)
    {
        try {
            // Making the class injectable into controller actions
            // by resolving the current request from the service container
            $this->request = $request ?? Container::getInstance()->make('request');

            // We set the current body, query and files attributes from the request context
            $this->update($request->all())->files($request->allFiles());

            // We set the current instance user resolver property
            $this->setUserResolver($this->request instanceof Request ? $this->request->getUserResolver() : function() {
                return null;
            });
        } catch (\Throwable $e) {
            // We catch the exception for it to not propagate
        }
    }
}
