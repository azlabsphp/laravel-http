<?php

namespace Drewlabs\Packages\Http\Requests;

use Drewlabs\Contracts\Validator\CoreValidatable as Validatable;

class LoginRequest implements Validatable
{
    /**
     * {@inheritDoc}
     * 
     * Validate an incoming Login Request inputs
     */
    public function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
            'remember_me' => 'sometimes|boolean'
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
