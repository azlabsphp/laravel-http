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

use Illuminate\Http\UploadedFile;

/**
 * @mixin \Drewlabs\Laravel\Http\Traits\HasAuthenticatable
 * @mixin \Drewlabs\Laravel\Http\Traits\AuthorizeRequest
 *
 * @property \Illuminate\Http\Request $request
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
     * @return self
     */
    public static function new($attributes = [])
    {
        return (new static())->merge($attributes ?? []);
    }

    /**
     * Set the attributes to validate on the validatable class.
     *
     * @return self
     */
    public function set(array $values = [])
    {
        $this->request = $this->request->replace($values);

        return $this;
    }

    /**
     * Copy the current object modifying the body attribute.
     *
     * @return self
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
     * @return self
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
     * @return self
     */
    public function update(array $values = [])
    {
        return $this->merge($values, true);
    }

    /**
     * Get a key from the user provided attributes.
     *
     * @param string $key
     *
     * @return array|mixed|null
     */
    public function get(string $key = null)
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
    public function file(string $key)
    {
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
