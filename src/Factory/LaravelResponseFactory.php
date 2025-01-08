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

namespace Drewlabs\Laravel\Http\Factory;

use Drewlabs\Http\Factory\ResponseFactoryInterface;
use Drewlabs\Overloadable\MethodCallExpection;
use Drewlabs\Overloadable\Overloadable;
use Drewlabs\Laravel\Http\StreamResponse;
use Illuminate\Http\Response;

use const PREG_SPLIT_NO_EMPTY;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

/**
 * @method Response|HttpFoundationResponse create($data, int $status = 200, array $headers = [], string $protocol = '1.1')
 * @method Response|HttpFoundationResponse create(\Psr\Http\Message\ResponseInterface $response, bool $streamed)
 */
class LaravelResponseFactory implements ResponseFactoryInterface
{
    use ContextResponseFactory;
    use Overloadable;

    /**
     * Creates class instance.
     *
     * @param callable|\Closure($content = '', $status = 200, array $headers = []): \Symfony\Component\HttpFoundation\Response $factory
     */
    public function __construct(?callable $factory = null)
    {
        $this->responseFactory = $factory ?? static::useDefaultFactory();
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     * @throws MethodCallExpection
     *
     * @return Response|HttpFoundationResponse
     */
    public function create(...$args)
    {
        return $this->overload($args, [
            function (ResponseInterface $psrResponse, bool $streamed = false) {
                $cookies = $psrResponse->getHeader('Set-Cookie');
                $psrResponse = $psrResponse->withoutHeader('Set-Cookie');
                $response = $streamed ? new StreamResponse($psrResponse->getBody(), $psrResponse->getStatusCode(), $psrResponse->getHeaders()) : $this->createResponse(
                    $psrResponse->getBody()->__toString(),
                    $psrResponse->getStatusCode(),
                    $psrResponse->getHeaders()
                );
                $response->setProtocolVersion($psrResponse->getProtocolVersion());
                foreach ($cookies as $cookie) {
                    $response->headers->setCookie($this->createCookie($cookie));
                }

                return $response;
            },
            function ($data, int $status = 200, array $headers = [], string $protocol = '1.1') {
                $response = $this->createResponse($data, $status, $headers ?? []);
                $response->setProtocolVersion($protocol ?? '1.1');

                return $response;
            },
        ]);
    }

    /**
     * Creates a Cookie instance from a cookie string.
     *
     * @throws \InvalidArgumentException
     *
     * @return Cookie
     */
    private function createCookie(string $string)
    {

        if (!$attributes = preg_split('/\s*;\s*/', $string, -1, PREG_SPLIT_NO_EMPTY)) {
            throw new \InvalidArgumentException(sprintf('The raw value of the `Set Cookie` header `%s` could not be parsed.', $string));
        }

        $composed = explode('=', array_shift($attributes), 2);
        $cookie = ['name' => $composed[0], 'value' => isset($composed[1]) ? urldecode($composed[1]) : ''];

        while ($attribute = array_shift($attributes)) {
            $attribute = explode('=', $attribute, 2);
            $name = strtolower($attribute[0]);
            $value = $attribute[1] ?? null;

            if (\in_array($name, ['expires', 'domain', 'path', 'samesite'], true)) {
                $cookie[$name] = $value;
                continue;
            }
            if (\in_array($name, ['secure', 'httponly'], true)) {
                $cookie[$name] = true;
                continue;
            }
            if ('max-age' === $name) {
                $cookie['expires'] = time() + (int) $value;
            }
        }

        return new Cookie(
            $cookie['name'],
            $cookie['value'],
            $cookie['expires'] ?? 0,
            $cookie['path'] ?? '/',
            $cookie['domain'] ?? null,
            $cookie['secure'] ?? false,
            $cookie['httponly'] ?? false,
            true,
            $cookie['samesite'] ?? Cookie::SAMESITE_LAX
        );
    }
}
