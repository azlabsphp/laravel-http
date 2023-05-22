<?php

namespace Drewlabs\Packages\Http\Factory;

use Closure;
use Drewlabs\Http\Factory\ViewResponseFactoryInterface;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\View;

class ViewResponseFactory implements ViewResponseFactoryInterface
{
    /**
     * @var \Closure():\Illuminate\Contracts\View\Factory
     */
    private $factoryResolver;

    /**
     * Creates class instance
     * 
     * @param callable|null|\Closure():Illuminate\Contracts\View\Factory $viewResolver 
     */
    public function __construct(callable $viewResolver = null)
    {
        $this->factoryResolver = $viewResolver ?? self::useDefault();
    }


    /**
     * Creates and return the default view factory resolver
     * 
     * @return \Closure():Illuminate\Contracts\View\Factory
     */
    private static function useDefault()
    {
        return function() {
            return Container::getInstance()->make('view');
        };
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
}
