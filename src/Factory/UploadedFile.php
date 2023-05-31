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

use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

use const UPLOAD_ERR_NO_FILE;

/**
 * @internal
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class UploadedFile extends HttpUploadedFile
{
    /**
     * @var UploadedFileInterface
     */
    private $psrUploadedFile;

    /**
     * @var bool
     */
    private $test = false;

    /**
     * Creates class instance.
     *
     * @throws \RuntimeException
     * @throws FileNotFoundException
     *
     * @return void
     */
    public function __construct(UploadedFileInterface $psrUploadedFile, callable $getTemporaryPath)
    {
        $error = $psrUploadedFile->getError();
        $path = '';
        if (UPLOAD_ERR_NO_FILE !== $error) {
            $path = $psrUploadedFile->getStream()->getMetadata('uri') ?? '';

            if ($this->test = !\is_string($path) || !is_uploaded_file($path)) {
                $path = $getTemporaryPath();
                $psrUploadedFile->moveTo($path);
            }
        }
        parent::__construct(
            $path,
            (string) $psrUploadedFile->getClientFilename(),
            $psrUploadedFile->getClientMediaType(),
            $psrUploadedFile->getError(),
            $this->test
        );

        $this->psrUploadedFile = $psrUploadedFile;
    }

    public function move($directory, $name = null): File
    {
        if (!$this->isValid() || $this->test) {
            return parent::move($directory, $name);
        }

        $target = $this->getTargetFile($directory, $name);

        try {
            $this->psrUploadedFile->moveTo((string) $target);
        } catch (\RuntimeException $e) {
            throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, $e->getMessage()), 0, $e);
        }

        @chmod($target, 0666 & ~umask());

        return $target;
    }
}
