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

use Drewlabs\Http\Factory\ViewResponseFactoryInterface;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

class ViewResponseFactory implements ViewResponseFactoryInterface
{
    /**
     * @var \Closure():ViewFactory
     */
    private $factoryResolver;

    /**
     * Creates class instance.
     *
     * @param callable|\Closure():ViewFactory|null $viewResolver
     */
    public function __construct(?callable $viewResolver = null)
    {
        $this->factoryResolver = $viewResolver ?? static::useDefault();
    }

    /**
     * {@inheritDoc}
     *
     * @return View|string
     */
    public function create(string $path, array $data = [])
    {
        $factory = ($this->factoryResolver)();

        return $factory->make($path, $data);
    }

    /**
     * Creates and return the default view factory resolver.
     *
     * @return \Closure():ViewFactory
     */
    private static function useDefault()
    {
        return static function () {
            return Container::getInstance()->make('view');
        };
    }
}
