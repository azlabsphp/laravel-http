<?php

namespace Drewlabs\Packages\Http\Factory;

use Drewlabs\Http\Factory\Psr\PsrRequestFactoryInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class PsrRequestFactory implements PsrRequestFactoryInterface
{
    /**
     * 
     * @var ServerRequestFactoryInterface
     */
    private $serverRequestFactory;

    /**
     * 
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * 
     * @var UploadedFileFactoryInterface
     */
    private $uploadedFileFactory;

    /**
     * Creates new class instance
     * 
     * @param ServerRequestFactoryInterface $serverRequestFactory 
     * @param StreamFactoryInterface $streamFactory 
     * @param UploadedFileFactoryInterface $uploadedFileFactory 
     * @return void 
     */
    public function __construct(
        ServerRequestFactoryInterface $serverRequestFactory,
        StreamFactoryInterface $streamFactory,
        UploadedFileFactoryInterface $uploadedFileFactory
    ) {
        $this->serverRequestFactory = $serverRequestFactory;
        $this->streamFactory = $streamFactory;
        $this->uploadedFileFactory = $uploadedFileFactory;
    }

    /**
     * {@inheritDoc}
     * 
     * @param HttpFoundationRequest $baseRequest 
     */
    public function create($baseRequest)
    {
        $uri = $baseRequest->server->get('QUERY_STRING', '');
        $uri = $baseRequest->getSchemeAndHttpHost() . $baseRequest->getBaseUrl() . $baseRequest->getPathInfo() . ('' !== $uri ? '?' . $uri : '');

        $request = $this->serverRequestFactory->createServerRequest($baseRequest->getMethod(), $uri, $baseRequest->server->all());

        $request = $this->setHeaders($request, $baseRequest->headers->all())
            ->withBody($this->streamFactory->createStreamFromResource($baseRequest->getContent(true)))
            ->withUploadedFiles($this->getFiles($baseRequest->files->all()))
            ->withCookieParams($baseRequest->cookies->all())
            ->withQueryParams($baseRequest->query->all())
            ->withParsedBody($baseRequest->request->all());

        foreach ($baseRequest->attributes->all() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $request;
    }

    /**
     *
     * @return array
     */
    /**
     * Converts framework uploaded files array to the PSR one.
     * 
     * @param array $uploadedFiles 
     * @return array 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    private function getFiles(array $uploadedFiles)
    {
        $files = [];
        foreach ($uploadedFiles as $key => $value) {
            if (null === $value) {
                $files[$key] = $this->uploadedFileFactory->createUploadedFile($this->streamFactory->createStream(), 0, \UPLOAD_ERR_NO_FILE);
                continue;
            }
            $files[$key] = $value instanceof UploadedFile ? $this->createPsrUploadedFile($value) :  $this->getFiles($value);
        }

        return $files;
    }

    /**
     * Creates a PSR-7 UploadedFile instance from a framework one.
     * 
     * @param UploadedFile $file 
     * @return UploadedFileInterface 
     * @throws RuntimeException 
     * @throws InvalidArgumentException 
     */
    private function createPsrUploadedFile(UploadedFile $file)
    {
        return $this->uploadedFileFactory->createUploadedFile(
            $this->streamFactory->createStreamFromFile(
                $file->getRealPath()
            ),
            (int) $file->getSize(),
            $file->getError(),
            $file->getClientOriginalName(),
            $file->getClientMimeType()
        );
    }


    private function setHeaders($request, array $headers)
    {

        foreach ($headers as $name => $value) {
            try {
                $request = $request->withHeader($name, $value);
            } catch (\InvalidArgumentException $e) {
            }
        }
        return $request;
    }
}
