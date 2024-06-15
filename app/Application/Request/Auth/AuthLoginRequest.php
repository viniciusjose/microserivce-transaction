<?php

namespace App\Application\Request\Auth;

use Hyperf\Validation\Request\FormRequest;

class AuthLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|max:150',
            'password' => 'required|string|max:100'
        ];
    }
}