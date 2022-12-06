<?php

use Drewlabs\Core\Helpers\Str;
use Drewlabs\Packages\Http\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

trait HasServerRequest
{

    /**
     *
     * @var ServerRequestInterface|\Illuminate\Http\Request|mixed
     */
    private $request;

    /**
     * Creates a view model instance
     * 
     * @param ServerRequestInterface|Request|mixed|null $request
     * @param mixed $request
     */
    public function __construct($request = null)
    {
        try {
            // Making the class injectable into controller actions
            // by resolving the current request from the service container
            $request = $request ?? self::createResolver('request')();
            if ($request instanceof ServerRequestInterface) {
                $this->fromPsrServerRequest($request);
            } else if ($request instanceof \Illuminate\Http\Request) {
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

    /**
     * Set view model attributes from Laravel request
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return mixed 
     */
    protected function fromLaravelRequest($request)
    {
        // TODO : Interact with symfony request in future release instead of
        // laravel request
        $resolver = $request->getUserResolver() ?? function () {
        };
        return $this->withBody($request->all())
            ->files($request->allFiles())
            ->setUserResolver($resolver);
    }

    protected function fromPsrServerRequest(ServerRequestInterface $request)
    {
        return $this->withBody(array_merge(
            $request->getQueryParams() ?: [],
            (array)($request->getParsedBody() ?? [])
        ))->files($request->getUploadedFiles());
    }

    /**
     * Return the request object 
     *
     * @return ServerRequestInterface|\Illuminate\Http\Request|mixed
     */
    public function request($request = null)
    {
        if (null !== $request) {
            $this->request = $request;
        }
        return $this->request;
    }



    /**
     * Returns the bearer token string for the current request
     * 
     * @return null|string 
     * @throws NotSupportedMessageException 
     * @throws NotSupportedMessageException 
     */
    public function bearerToken()
    {
        if (null === $this->request) {
            return null;
        }
        $request = new ServerRequest($this->request);
        $header = $request->getHeader('authorization');
        if (null === $header) {
            return null;
        }
        if (!Str::startsWith(strtolower($header), 'bearer')) {
            return null;
        }
        return trim(str_ireplace('bearer', '', $header));
    }

    /**
     * 
     * @return array 
     * @throws NotSupportedMessageException 
     * @throws NotSupportedMessageException 
     */
    public function basicAuth()
    {
        if (null === $this->request) {
            return [];
        }
        $request = new ServerRequest($this->request);
        $header = $request->getHeader('authorization');
        if (null === $header) {
            return [];
        }
        if (!Str::startsWith(strtolower($header), 'basic')) {
            return [];
        }
        $base64 = trim(str_ireplace('basic', '', $header));

        return explode(':', \base64_decode($base64));
    }
}
