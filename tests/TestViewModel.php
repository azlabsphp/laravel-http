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

namespace Drewlabs\Laravel\Http\Tests;

use Drewlabs\Contracts\Validator\ViewModel;
use Drewlabs\Laravel\Http\Traits\HttpViewModel;
use Drewlabs\Laravel\Http\Traits\InteractsWithServerRequest;
use Drewlabs\Validation\Traits\ModelAware;
use Drewlabs\Validation\Traits\ProvidesRulesFactory;
use Drewlabs\Validation\Traits\Validatable;
use Illuminate\Http\Request;

class TestViewModel implements ViewModel
{
    use HttpViewModel;
    use InteractsWithServerRequest;
    use ModelAware;
    use ProvidesRulesFactory;
    use Validatable;

    /**
     * Creates class instance.
     */
    public function __construct(Request $request = null)
    {
        $this->bootInstance($request);
    }

    public function rules()
    {
        return [];
    }

    public function messages()
    {
        return [];
    }
}
