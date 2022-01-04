<?php

namespace Drewlabs\Packages\Http\Traits;

use Drewlabs\Core\Validator\Traits\ViewModel;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;

trait HttpViewModel
{
    use ViewModel;

    private function __construct()
    {
    }

    public function fromIlluminateHttpRequest(Request $request)
    {
        return $this->setUser($request->user())
            ->files($request->allFiles())
            ->withBody($request->all());
    }

    public function fromPsrServerRequest(ServerRequestInterface $request)
    {
        return $this->withBody(
            array_merge(
                (array)($request->getParsedBody() ?? []),
                $request->getQueryParams()
            )
        );
    }

    public static function create($request)
    {
        if ($request instanceof ServerRequestInterface) {
            return (new self)->fromPsrServerRequest($request);
        }
        if ($request instanceof Request) {
            return (new self)->fromIlluminateHttpRequest($request);
        }
        return new self;
    }
}
