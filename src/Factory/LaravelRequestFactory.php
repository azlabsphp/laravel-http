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

use Drewlabs\Http\Factory\RequestFactoryInterface;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class LaravelRequestFactory implements RequestFactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return Request
     */
    public function create(ServerRequestInterface $psrRequest, bool $streamed = false)
    {
        $server = [];
        $uri = $psrRequest->getUri();

        if ($uri instanceof UriInterface) {
            $server['SERVER_NAME'] = $uri->getHost();
            $server['SERVER_PORT'] = $uri->getPort() ?: ('https' === $uri->getScheme() ? 443 : 80);
            $server['REQUEST_URI'] = $uri->getPath();
            $server['QUERY_STRING'] = $uri->getQuery();
            if ('' !== $server['QUERY_STRING']) {
                $server['REQUEST_URI'] .= '?'.$server['QUERY_STRING'];
            }
            if ('https' === $uri->getScheme()) {
                $server['HTTPS'] = 'on';
            }
        }

        $server['REQUEST_METHOD'] = $psrRequest->getMethod();

        $request = new Request(
            $psrRequest->getQueryParams(),
            \is_array($parsedBody = $psrRequest->getParsedBody()) ? $parsedBody : [],
            $psrRequest->getAttributes(),
            $psrRequest->getCookieParams(),
            $this->getFiles($psrRequest->getUploadedFiles()),
            array_replace($psrRequest->getServerParams(), $server),
            $streamed ? $psrRequest->getBody()->detach() : $psrRequest->getBody()->__toString()
        );
        $request->headers->add($psrRequest->getHeaders());

        return $request;
    }

    /**
     * Gets a temporary file path.
     *
     * @return string
     */
    protected function getTemporaryPath()
    {
        return tempnam(sys_get_temp_dir(), uniqid('drewlabs', true));
    }

    /**
     * Converts to the input array to $_FILES structure.
     */
    private function getFiles(array $uploadedFiles): array
    {
        $files = [];
        foreach ($uploadedFiles as $key => $value) {
            $files[$key] = $value instanceof UploadedFileInterface ? $this->createUploadedFile($value) : $this->getFiles($value);
        }

        return $files;
    }

    /**
     * Creates framework UploadedFile instance from PSR-7 ones.
     *
     * @return Drewlabs\Laravel\Http\Factory\UploadedFile
     */
    private function createUploadedFile(UploadedFileInterface $psrUploadedFile): UploadedFile
    {
        return new UploadedFile($psrUploadedFile, function () {
            return $this->getTemporaryPath();
        });
    }
}
