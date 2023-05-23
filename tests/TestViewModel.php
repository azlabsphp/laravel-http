<?php

namespace Drewlabs\Packages\Http\Tests;

use Drewlabs\Packages\Http\Traits\HttpViewModel;
use Drewlabs\Validation\Traits\ProvidesRulesFactory;
use Drewlabs\Validation\Traits\Validatable;
use Drewlabs\Validation\Traits\ViewModel;
use Illuminate\Http\Request;

class TestViewModel
{
    use HttpViewModel;
    use Validatable;
    use ViewModel;
    use ProvidesRulesFactory;

    public function __construct(Request $request = null)
    {
        $this->buildInstanceFromRequestAttibutes($request);
    }

}