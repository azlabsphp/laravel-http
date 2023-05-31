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

use Illuminate\Container\Container;
use Psr\Container\ContainerInterface;

trait ContainerAware
{
    /**
     * Create an abstract implementation from framework context.
     *
     * @param mixed $abstract
     *
     * @return \Closure
     */
    protected static function createResolver($abstract = null)
    {
        /*
         * @param ContainerInterface|null $context
         * @return mixed
         */
        return static function ($context = null) use ($abstract) {
            if ($context) {
                return null === $abstract ? $context : $context->get($abstract);
            }

            return null === $abstract ? Container::getInstance() : Container::getInstance()->make($abstract);
        };
    }
}
