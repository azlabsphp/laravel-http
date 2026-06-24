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

namespace Drewlabs\Laravel\Http\Guards;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Override;

class GuestGuard implements Guard
{
    /** @var Authenticatable */
    private $user;

    /**
     * Create a new guard instance.
     *
     * @return static
     */
    public function __construct()
    {
    }

    #[Override]
    public function check()
    {
        return false;
    }

    #[Override]
    public function guest()
    {
        return true;
    }

    #[Override]
    public function user()
    {
        return $this->user;
    }

    #[Override]
    public function id()
    {
        return $this->user ? $this->user->getAuthIdentifier() : null;
    }

    #[Override]
    public function validate(array $credentials = [])
    {
        return false;
    }

    #[Override]
    public function hasUser()
    {
        return null !== $this->user;
    }

    #[Override]
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }
}
