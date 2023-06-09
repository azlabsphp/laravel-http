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

use Drewlabs\Http\Factory\Psr\PsrResponseFactoryInterface;
use Drewlabs\Http\ReasonPhrase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PsrResponseFactory implements PsrResponseFactoryInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * Creates class instance.
     */
    public function __construct(StreamFactoryInterface $streamFactory, ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @param Response $baseResponse
     */
    public function create($baseResponse)
    {
        $response = $this->responseFactory->createResponse($baseResponse->getStatusCode(), ReasonPhrase::get($baseResponse->getStatusCode()) ?? '');
        if ($baseResponse instanceof BinaryFileResponse && !$baseResponse->headers->has('Content-Range')) {
            $stream = $this->streamFactory->createStreamFromFile($baseResponse->getFile()->getPathname());
        } elseif ($baseResponse instanceof StreamedResponse || $baseResponse instanceof BinaryFileResponse) {
            $stream = $this->createWritableStream();
            ob_start(static function ($buffer) use ($stream) {
                $stream->write($buffer);

                return '';
            }, 1);
            $baseResponse->sendContent();
            ob_end_clean();
        } else {
            $stream = $this->createWritableStream();
            $stream->write($baseResponse->getContent());
        }

        $response = $response->withBody($stream);
        $headers = $baseResponse->headers->all();
        if (!empty($cookies = $baseResponse->headers->getCookies())) {
            $headers['Set-Cookie'] = [];
            foreach ($cookies as $cookie) {
                $headers['Set-Cookie'][] = $cookie->__toString();
            }
        }

        return $this->setHeaders($response, $headers)->withProtocolVersion($baseResponse->getProtocolVersion());
    }

    /**
     * Creates a writable PHP Psr stream.
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return StreamInterface
     */
    private function createWritableStream()
    {
        return $this->streamFactory->createStreamFromFile('php://temp', 'wb+');
    }

    private function setHeaders(MessageInterface $message, array $headers)
    {
        foreach ($headers as $name => $value) {
            try {
                $message = $message->withHeader($name, $value);
            } catch (\InvalidArgumentException $e) {
            }
        }

        return $message;
    }
}
