<?php

namespace Drewlabs\Packages\Http\Guards;

use Illuminate\Http\Request;

class AnonymousGuard
{

    /**
     * Create a new guard instance.
     * 
     * @return self
     */
    public function __construct()
    {
    }

    /**
     * Retrieve the authenticated user for the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        return;
    }
}
