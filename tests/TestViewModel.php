<?php

namespace Drewlabs\Packages\Http\Tests;

use Drewlabs\Contracts\Validator\ViewModel;
use Drewlabs\Packages\Http\Traits\HttpViewModel;
use Drewlabs\Packages\Http\Traits\InteractsWithServerRequest;
use Drewlabs\Validation\Traits\ModelAware;
use Drewlabs\Validation\Traits\ProvidesRulesFactory;
use Drewlabs\Validation\Traits\Validatable;
use Illuminate\Http\Request;

class TestViewModel implements ViewModel
{
    use HttpViewModel;
    use Validatable;
    use InteractsWithServerRequest;
    use ProvidesRulesFactory;
    use ModelAware;

    public function __construct(Request $request = null)
    {
        $this->buildInstanceFromRequestAttibutes($request);
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
