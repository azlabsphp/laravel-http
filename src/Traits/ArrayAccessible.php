<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Laravel\Http\Traits;

/**
 * @property \Illuminate\Http\Request $request
 * @method \Illuminate\Http\Request request()
 */
trait ArrayAccessible
{
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->request->offsetExists($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->request->offsetGet($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        return $this->request->offsetSet($offset, $value);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->request->offsetUnset($offset);
    }
}
