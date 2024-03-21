<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Laravel\Http\Traits;

use Drewlabs\Core\Helpers\Str;
use Drewlabs\Validation\Traits\PreparesInputs;
use Illuminate\Container\Container;
use Illuminate\Http\Request;

/**
 * @method mixed __call(string $name, $arguments)
 */
trait HttpViewModel
{
    use ArrayAccessible;
    use AuthorizeRequest;
    use ContainerAware;
    use HasAuthenticatable;
    use PreparesInputs;

    /**
     * @var Request|HttpFoundationRequest
     */
    private $request;

    /**
     * Query `bearer token` header value from context request.
     *
     * @return string|null
     */
    public function bearerToken()
    {
        if (null === $this->request) {
            return null;
        }
        if (null === ($header = $this->request->headers->get('authorization'))) {
            return null;
        }
        if (!Str::startsWith(strtolower($header), 'bearer')) {
            return null;
        }

        return trim(str_ireplace('bearer', '', $header));
    }

    /**
     * Query `basic auth` header value from context request instance.
     *
     * It returns a tuple consisting of [$username, $password]
     *
     * @return array<string>
     */
    public function basicAuth()
    {
        if (null === $this->request) {
            return [];
        }
        if (null === ($header = $this->request->headers->get('authorization'))) {
            return [];
        }
        if (!Str::startsWith(strtolower($header), 'basic')) {
            return [];
        }
        $base64 = trim(str_ireplace('basic', '', $header));

        return explode(':', base64_decode($base64, true));
    }

    /**
     * Context request setter and getter.
     *
     * @param Request|HttpFoundationRequest|null $request
     *
     * @return Request|HttpFoundationRequest
     */
    public function request($request = null)
    {
        if (null !== $request) {
            $this->request = $request;
        }

        return $this->request;
    }

    /**
     * Boot the current instance setting required properties values
     * from request parameters.
     *
     * @param Request $request
     *
     * @return void
     */
    private function bootInstance($request = null)
    {
        try {
            // Making the class injectable into controller actions
            // by resolving the current request from the service container
            $this->request = $request ?? Container::getInstance()->make('request');

            // We set the current instance user resolver property
            $this->setUserResolver($this->request instanceof Request ? $this->request->getUserResolver() : static function () {
                return null;
            });
        } catch (\Throwable $e) {
            // We catch the exception for it to not propagate
        }

    }
}
