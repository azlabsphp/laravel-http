<?php

namespace Drewlabs\Packages\Http\Contracts;

use Drewlabs\Contracts\Validator\Validator;
use Drewlabs\Contracts\Data\EnumerableQueryResult;

/**
 * @deprecated v2.4.x
 * 
 * @package Drewlabs\Packages\Http\Contracts
 */
interface ControllerActionHandler
{

    /**
     * Apply gate policy on the request workflow
     *
     * @param \Closure|callable|bool $callback
     * @param \Illuminate\Http\Request|\Psr\Http\Message\RequestInterface $request
     * @param array|null $params
     * @return bool
     */
    public function applyPolicy($callback, $request, ...$params);

    /**
     * Transform request object before passing it down to 
     *
     * @param \Closure|callable $callback
     * @param  \Illuminate\Http\Request|\Psr\Http\Message\RequestInterface $request
     * @param array $params
     *
     * @return array
     */
    public function before($callback, $request, ...$params);

    /**
     * Apply validation on request inputs
     *
     * @param \Closure|callable|bool|null $callback
     * @param  \Illuminate\Http\Request|\Psr\Http\Message\RequestInterface $request
     * @param ValidatorContract $validator
     * @param array $params
     *
     * @return array|null
     */
    public function validate($callback, $request, Validator $validator, ...$params);

    /**
     * Apply data transformation 
     *
     * @param \Closure|callable|null $callback
     * @param EnumerableQueryResult|mixed $body
     * @param array $params
     * @return array|mixed
     */
    public function after($callback, $result, ...$params);
}