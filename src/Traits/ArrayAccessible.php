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

/**
 * @property \Illuminate\Http\Request $request
 *
 * @method \Illuminate\Http\Request request()
 */
trait ArrayAccessible
{
    /** @param mixed $offset */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->request->offsetExists($offset);
    }

    /** @param mixed $offset */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->request->offsetGet($offset);
    }

    /** 
     * @param mixed $offset
     * @param mixed $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        return $this->request->offsetSet($offset, $value);
    }

    /** @param mixed $offset */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->request->offsetUnset($offset);
    }
}
