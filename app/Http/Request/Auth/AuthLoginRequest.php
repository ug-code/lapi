<?php

namespace App\Http\Request\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthLoginRequest extends FormRequest
{
    public function rules()
    {

        return [
            'email'    => 'required',
            'password' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'email'    =>  'email',
            'password' =>  'password',
        ];
    }

}
