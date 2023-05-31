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

use Symfony\Component\HttpFoundation\ParameterBag;

abstract class Transform
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $this->clean($request);

        return $next($request);
    }

    /**
     * Clean the request's data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function clean($request)
    {
        $this->cleanParameterBag($request->query);

        if ($request->isJson()) {
            $this->cleanParameterBag($request->json());
        } elseif ($request->request !== $request->query) {
            $this->cleanParameterBag($request->request);
        }
    }

    /**
     * Clean the data in the parameter bag.
     *
     * @return void
     */
    protected function cleanParameterBag(ParameterBag $bag)
    {
        $bag->replace($this->cleanArray($bag->all()));
    }

    /**
     * Clean the data in the given array.
     *
     * @param string $keyPrefix
     *
     * @return array
     */
    protected function cleanArray(array $data, $keyPrefix = '')
    {
        return collect($data)->map(function ($value, $key) use ($keyPrefix) {
            return $this->cleanValue($keyPrefix.$key, $value);
        })->all();
    }

    /**
     * Clean the given value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function cleanValue($key, $value)
    {
        if (\is_array($value)) {
            return $this->cleanArray($value, $key.'.');
        }

        return $this->transform($key, $value);
    }

    /**
     * Transform the given value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    abstract protected function transform($key, $value);
}
