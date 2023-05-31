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

namespace Drewlabs\Laravel\Http\Middleware;

class EmptyStringToNull extends Transform
{
    protected function transform($key, $value)
    {
        return \is_string($value) && '' === $value ? null : $value;
    }
}
