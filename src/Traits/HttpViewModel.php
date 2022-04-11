<?php

namespace Drewlabs\Packages\Http\Traits;

use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;

trait HttpViewModel
{
    use \Drewlabs\Core\Validator\Traits\ViewModel;
    use ContainerAware;

    /**
     * @param ServerRequestInterface|Request|mixed|null $request 
     * @param mixed $request 
     * @return self 
     */
    public function __construct($request = null)
    {
        try {
            // Making the class injectable into controller actions
            // by resolving the current request from the service container 
            $request = $request ?? self::createResolver('request')();
            if ($request instanceof ServerRequestInterface) {
                $this->fromPsrServerRequest($request);
            } else if ($request instanceof Request) {
                $this->fromLaravelRequest($request);
            }
        } catch (\Throwable $e) {
            // We catch the exception for it to not propagate
        }
    }

    /**
     * Creates an instance of the viewModel class
     * 
     * @param ServerRequestInterface|Request|mixed $request 
     * @param ContainerInterface|\ArrayAccess|null $container 
     * @return HttpViewModel 
     */
    public static function create($request = null, $container = null)
    {
        $request = $request ?: self::createResolver(Request::class)($container);
        return new self($request);
    }

    protected function fromLaravelRequest(Request $request)
    {
        return $this->setUser($request->user())
            ->withBody($request->all())
            ->files($request->allFiles());
    }

    protected function fromPsrServerRequest(ServerRequestInterface $request)
    {
        $body = array_merge(
            $request->getQueryParams() ?: [],
            (array)($request->getParsedBody() ?? [])
        );
        return $this->withBody($body)
            ->files($request->getUploadedFiles());
    }
}
