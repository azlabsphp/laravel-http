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
     * 
     * @var mixed
     */
    private $request;

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
            $this->request = $request;
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
        return new self($request ?? self::createResolver('request')($container));
    }

    protected function fromLaravelRequest(Request $request)
    {
        return $this->withBody($request->all())
            ->files($request->allFiles())
            ->setUserResolver($request->getUserResolver() ?? function () {
                // Returns an empty resolver if the request does not provide one
                //
            });
    }

    protected function fromPsrServerRequest(ServerRequestInterface $request)
    {
        return $this->withBody(
            array_merge(
                $request->getQueryParams() ?: [],
                (array)($request->getParsedBody() ?? [])
            )
        )->files($request->getUploadedFiles());
    }

    /**
     * 
     * @return mixed 
     */
    public function request($request = null)
    {
        if (null !== $request) {
            $this->request = $request;
        }
        return $this->request;
    }
}
