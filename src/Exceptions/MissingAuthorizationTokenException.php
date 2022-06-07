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

namespace Drewlabs\Packages\Http\Exceptions;

/**
 * 
 * @package Drewlabs\Packages\Http\Exceptions
 */
class MissingAuthorizationTokenException extends HttpException
{

    /**
     * Creates an instance of HttpException
     * 
     * @return self 
     */
    public function __construct()
    {
        parent::__construct(401, 'missing authorization token');
    }
}