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

namespace Drewlabs\Packages\Http\Traits;

/**
 * @deprecated v2.1.x Use {@see \Drewlabs\Packages\Http\Traits\ContainerAware} mixin instead
 */
trait HasIocContainer
{
    use ContainerAware;
}
