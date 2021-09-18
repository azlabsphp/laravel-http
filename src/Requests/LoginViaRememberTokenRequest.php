<?php

namespace Drewlabs\Packages\Http\Requests;

use Drewlabs\Contracts\Validator\CoreValidatable as Validatable;
class LoginViaRememberTokenRequest implements Validatable
{
    /**
     * {@inheritDoc}
     * 
     * Validate an incoming Login Request inputs
     */
    public function rules()
    {
        return [
            'identifier' => 'required',
            'remember_token' => 'required'
        ];
    }

    /**
     * {@inheritDoc}
     * 
     * Returns validation error when login request validation fails
     */
    public function messages()
    {
        return [];
    }
}
