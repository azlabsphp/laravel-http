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

use Illuminate\Http\Request;

class GuessGuard
{
    /**
     * Create a new guard instance.
     *
     * @return self
     */
    public function __construct()
    {
    }

    /**
     * Retrieve the authenticated user for the incoming request.
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {

    }
}
