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

use Drewlabs\Contracts\Auth\Authenticatable;

trait HasAuthenticatable
{
    /**
     * @var callable
     */
    private $userResolver;

    /**
     * @return self
     */
    public function setUser(Authenticatable $user = null)
    {
        return $this->setUserResolver(static function () use ($user) {
            return $user;
        });
    }

    /**
     * Provide a closure that when invoked return an instance of {@link Authenticatable}  class.
     *
     * ```
     * $model = $model->setUserResolver(function() use () {
     *  // Returns an authenticatable object
     * });
     * ```
     * 
     * @param callable $resolver
     * 
     * @return self 
     */
    public function setUserResolver(callable $resolver)
    {
        $this->userResolver = $resolver;
        return $this;
    }

    /**
     * Returns the authenticatable binded to the current object.
     *
     * @param string|null $guard
     *
     * @return Authenticatable|mixed
     */
    public function user($guard = null)
    {
        return $this->userResolver ? ($this->userResolver)($guard) : null;
    }

    /**
     * Add / Set value of the provided key to equals the id if the currently connected user.
     * 
     * @param string $key 
     * @return self 
     */
    public function setAuthUserInput(string $key)
    {
        return $this->merge([$key => (null !== ($user = $this->user())) ? $user->authIdentifier() : null]);
    }
}
