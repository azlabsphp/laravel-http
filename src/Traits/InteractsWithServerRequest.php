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

use Drewlabs\Laravel\Http\MimeTypes;
use Illuminate\Http\UploadedFile;

/**
 * @mixin \Drewlabs\Laravel\Http\Traits\HasAuthenticatable
 * @mixin \Drewlabs\Laravel\Http\Traits\AuthorizeRequest
 *
 * @property \Illuminate\Http\Request $request
 *
 * @method mixed __call(string $name, $arguments)
 */
trait InteractsWithServerRequest
{
    /**
     * @param mixed $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Creates an instance of the current class.
     *
     * @param array $attributes
     *
     * @return static
     */
    public static function new($attributes = [])
    {
        return (new static())->merge($attributes ?? []);
    }

    /**
     * Set the attributes to validate on the validatable class.
     *
     * @return static
     */
    public function set(array $values = [])
    {
        $this->request = $this->request->replace($values);

        return $this;
    }

    /**
     * Copy the current object modifying the body attribute.
     *
     * @return static
     */
    public function withBody(array $values = [])
    {
        $self = clone $this;
        $self->set($values ?? []);

        return $self;
    }

    /**
     * Merge the object inputs with some new values provided.
     *
     * **Note** By default, the merge method, return a modified
     * copy of the object. To modify object internal state instead,
     * pass `true` as second parameter to the merge call `merge([...], true)`
     * or call the `update([...])` to modify the object internal state
     *
     * @return static
     */
    public function merge(array $values = [], bool $mutate = false)
    {
        $self = $mutate ? $this : clone $this;
        $self->request->merge($values);

        return $self;
    }

    /**
     * Update object internal state with the provided values.
     *
     * @return static
     */
    public function update(array $values = [])
    {
        return $this->merge($values, true);
    }

    /**
     * Get a key from the user provided attributes.
     *
     * @return array|mixed|null
     */
    public function get(?string $key = null)
    {
        return $this->request->input($key);
    }

    /**
     * Checks if the view model has a given key.
     *
     * @return bool
     */
    public function has(string $key)
    {
        return $this->request->has($key);
    }

    /**
     * Get an entry from the inputs attributes.
     *
     * @return mixed|array
     */
    public function input($key = null, $default = null)
    {
        return $this->request->input($key, $default);
    }

    /**
     * Return the list of items in the object cache.
     *
     * @param array|mixed|null $keys
     *
     * @return array
     */
    public function all($keys = null)
    {
        return $this->request->all($keys);
    }

    // #region uploaded files methods
    /**
     * Get a file from the list of attached files.
     *
     * @return UploadedFile[]|UploadedFile
     */
    public function file(string $key, $value = null)
    {
        if (null !== $value) {
            // case the provided file is an instance of splfileinfo
            // we create Symfony uploaded file instance from the object
            if ($value instanceof \SplFileInfo) {
                $value = UploadedFile::createFromBase(new \Symfony\Component\HttpFoundation\File\UploadedFile($value->getPathname(), $value->getBasename(), MimeTypes::get($value->getExtension())));
            }
            $this->request->files->set($key, $value);
        }

        return $this->request->file($key);
    }

    /**
     * Returns the list of files attached to the current object.
     *
     * @return UploadedFile[]
     */
    public function allFiles()
    {
        return $this->request->allFiles();
    }

    /**
     * Determine if the uploaded data contains a file with matching key.
     *
     * @return bool
     */
    public function hasFile(string $key)
    {
        return $this->request->hasFile($key);
    }
    // #endregion uploaded files methods

    // #region Miscelanous methods
    /**
     * Returns the list of request inputs execept files contents.
     *
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->all() ?? [];
    }

    /**
     * Returns all request input except user provided keys.
     *
     * @param array $keys
     *
     * @return array
     */
    public function except($keys = [])
    {
        return $this->request->except($keys);
    }
    // #endregion Miscelanous methods
}
