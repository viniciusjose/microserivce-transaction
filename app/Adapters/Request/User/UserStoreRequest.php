<?php

declare(strict_types=1);

namespace App\Adapters\Request\User;

use Hyperf\Validation\Request\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'userType' => 'required|string|in:user,salesman',
            'email' => 'required|email|max:150',
            'password' => 'required|string',
            'identify' => 'required|string|max:11'
        ];
    }
}
