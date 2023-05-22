<?php

namespace Drewlabs\Packages\Http;

use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StreamResponse extends Response
{
    /**
     * @var StreamInterface
     */
    protected $stream;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $maxLength;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * Creates a {@see Psr7StreamResponse} class instance
     * 
     * @param StreamInterface $stream 
     * @param int $status 
     * @param array $headers 
     * @return void 
     * @throws InvalidArgumentException 
     */
    public function __construct(StreamInterface $stream, $status = 200, $headers = [])
    {
        parent::__construct(null, $status, $headers);
        $this->setStream($stream);
    }

    /**
     * Creates a Psr7StreamResponse from a disk path
     * 
     * @param string|\SplFileInfo $path 
     * @param int $status 
     * @param array $headers 
     * @return static 
     */
    public static function new($path, $status = 200, $headers = [])
    {
        if ($path instanceof \SplFileInfo) {
            $path = ($realpath = $path->getRealPath()) === false ? '' : $realpath;
        }
        if ((null === $path) || !is_string($path)) {
            throw new InvalidArgumentException('$path argument must be of type string or \SplFileInfo::class');
        }
        if (!@is_file($path)) {
            $response = new static((new Psr17Factory)->createStream(''), $status, $headers ?? []);
            return $response->withContentType('application/octect-stream');
        }
        $contentType = MimeTypes::get(pathinfo($path, PATHINFO_EXTENSION));
        if (!empty($contentType) && array_key_exists('Content-Type', $headers)) {
            $headers['Content-Type'] = $contentType;
        }
        if (!empty($contentType) && array_key_exists('content-type', $headers)) {
            $headers['content-type'] = $contentType;
        }
        $response = new static((new Psr17Factory)->createStreamFromFile($path), $status, $headers ?? []);
        if (null === $contentType) {
            $contentType = class_exists(\Symfony\Component\Mime\MimeTypes::class) ?
                forward_static_call([\Symfony\Component\Mime\MimeTypes::class, 'getDefault'])->guessMimeType($path) :
                'application/octect-stream';
        }
        return $response->withContentType($contentType);
    }

    /**
     * Sets the file to stream.
     *
     * @param StreamInterface $stream
     *
     * @return $this
     */
    public function setStream(StreamInterface $stream)
    {
        $this->stream = $stream;
        return $this;
    }

    /**
     * @return StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Sets the Content-Disposition header with the given filename.
     *
     * @param string $filename Use this UTF-8 encoded filename instead of the real name of the file
     * @param string $disposition ResponseHeaderBag::DISPOSITION_INLINE or ResponseHeaderBag::DISPOSITION_ATTACHMENT
     *
     * @return static
     */
    public function setContentDisposition($filename, $disposition = 'attachment')
    {
        $value = $this->headers->makeDisposition($disposition, $filename);
        $this->headers->set('Content-Disposition', $value);
        return $this;
    }

    /**
     * Set the request content type header
     * 
     * @param string $mimeType 
     * @return static 
     * @throws InvalidArgumentException 
     */
    public function withContentType(string $mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function prepare(Request $request): static
    {
        $this->headers->set('Content-Length', $this->stream->getSize());
        if (!$this->headers->has('Accept-Ranges')) {
            // Only accept ranges on safe HTTP methods
            $this->headers->set('Accept-Ranges', $request->isMethodSafe(false) ? 'bytes' : 'none');
        }
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', $this->mimeType ?? 'application/octet-stream');
        }
        if ('HTTP/1.0' !== $request->server->get('SERVER_PROTOCOL')) {
            $this->setProtocolVersion('1.1');
        }
        $this->ensureIEOverSSLCompatibility($request);
        $this->offset = 0;
        $this->maxLength = -1;
        $this->processRequestRange($request);
        return $this;
    }


    private function processRequestRange(Request $request)
    {
        if (!$request->headers->has('Range')) {
            return;
        }
        if (!(!$request->headers->has('If-Range') || $this->hasValidIfRangeHeader($request->headers->get('If-Range')))) {
            return;
        }

        $range = $request->headers->get('Range');
        $size = $this->stream->getSize();
        [$start, $end] = explode('-', substr($range, 6), 2) + array(0);
        $end = '' === ($value = trim($end)) ? $size - 1 : intval($value);
        if ('' === trim($start)) {
            $start = $size - $end;
            $end = $size - 1;
        } else {
            $start = (int)$start;
        }
        if ($start <= $end) {
            return;
        }
        if ($start < 0 || $end > $size - 1) {
            $this->setStatusCode(416);
            $this->headers->set('Content-Range', sprintf('bytes */%s', $size));
            return;
        }
        if (0 !== $start || $end !== $size - 1) {
            $this->maxLength = $end < $size ? $end - $start + 1 : -1;
            $this->offset = $start;
            $this->setStatusCode(206);
            $this->headers->set('Content-Range', sprintf('bytes %s-%s/%s', $start, $end, $size));
            $this->headers->set('Content-Length', $end - $start + 1);
        }
    }


    private function hasValidIfRangeHeader($header)
    {
        if ($this->getEtag() === $header) {
            return true;
        }
        if (null === ($lastModified = $this->getLastModified())) {
            return false;
        }
        return $lastModified->format('D, d M Y H:i:s') . ' GMT' === $header;
    }

    /**
     * Sends the file.
     *
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function sendContent(): static
    {
        if (!$this->isSuccessful()) {
            return parent::sendContent();
        }
        if (0 === $this->maxLength) {
            return $this;
        }
        $this->stream->seek($this->offset);
        $this->maxLength = $this->maxLength === -1 ? $this->stream->getSize() - $this->offset : $this->maxLength;
        $this->content = $this->stream->read($this->maxLength);
        return parent::sendContent();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException when the content is not null
     */
    #[\ReturnTypeWillChange]
    public function setContent($content): static
    {
        if (null !== $content) {
            throw new \LogicException('The content cannot be set on a Psr7StreamResponse instance.');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return false
     */
    #[\ReturnTypeWillChange]
    public function getContent(): string|false
    {
        return false;
    }
}
