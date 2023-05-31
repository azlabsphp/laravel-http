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

use const DEBUG_BACKTRACE_IGNORE_ARGS;

use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Access\Gate;

trait AuthorizeRequest
{
    /**
     * Authorize a given action against a set of arguments.
     *
     * @param mixed       $ability
     * @param mixed|array $arguments
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Auth\Access\Response
     */
    public function authorize($ability, $arguments = [])
    {
        [$ability, $arguments] = $this->parseAbilityAndArguments($ability, $arguments);

        return $this->getGateInstance()->authorize($ability, $arguments);
    }

    /**
     * Authorize a given action for a user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable|mixed $user
     * @param mixed                                            $ability
     * @param mixed|array                                      $arguments
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Auth\Access\Response
     */
    public function authorizeForUser($user, $ability, $arguments = [])
    {
        [$ability, $arguments] = $this->parseAbilityAndArguments($ability, $arguments);

        return $this->getGateInstance()->forUser($user)->authorize($ability, $arguments);
    }

    /**
     * Guesses the ability's name if it wasn't provided.
     *
     * @param mixed       $ability
     * @param mixed|array $arguments
     *
     * @return array
     */
    protected function parseAbilityAndArguments($ability, $arguments)
    {
        if (\is_string($ability)) {
            return [$ability, $arguments];
        }

        return [debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'], $ability];
    }

    /**
     * Creates/Get a `Gate` instance.
     *
     * @return Gate
     */
    private function getGateInstance()
    {
        return Container::getInstance()->make(Gate::class);
    }
}
