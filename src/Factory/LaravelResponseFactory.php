<?php

namespace Drewlabs\Packages\Http\Factory;

use Drewlabs\Http\Factory\ResponseFactoryInterface;
use Drewlabs\Overloadable\Overloadable;
use Drewlabs\Packages\Http\StreamResponse;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * 
 * @method TResponse create($data, int $status = 200, array $headers = [], string $protocol = '1.1')
 * @method TResponse create(\Psr\Http\Message\ResponseInterface $response, bool $streamed)
 * @package Drewlabs\Packages\Http\Factory
 */
class LaravelResponseFactory implements ResponseFactoryInterface
{
    use Overloadable;
    use ContextResponseFactory;

    /**
     * Creates class instance
     * 
     * @param callable|\Closure($content = '', $status = 200, array $headers = []): \Symfony\Component\HttpFoundation\Response $factory 
     */
    public function __construct(callable $factory = null)
    {
        $this->responseFactory = $factory ?? self::useDefault();
    }

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
            }
        ]);
    }

    /**
     * Creates a Cookie instance from a cookie string.
     * 
     * @param string $cookie 
     * @return Cookie 
     * @throws InvalidArgumentException 
     */
    private function createCookie(string $string)
    {

        if (!$attributes = \preg_split('/\s*;\s*/', $string, -1, \PREG_SPLIT_NO_EMPTY)) {
            throw new \InvalidArgumentException(\sprintf('The raw value of the `Set Cookie` header `%s` could not be parsed.', $string));
        }

        $composed = \explode('=', \array_shift($attributes), 2);
        $cookie = ['name' => $composed[0], 'value' => isset($composed[1]) ? \urldecode($composed[1]) : ''];

        while ($attribute = \array_shift($attributes)) {
            $attribute = \explode('=', $attribute, 2);
            $name = \strtolower($attribute[0]);
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
                $cookie['expires'] = \time() + (int) $value;
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
