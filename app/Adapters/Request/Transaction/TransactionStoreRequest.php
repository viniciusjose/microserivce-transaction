<?php

declare(strict_types=1);

namespace App\Adapters\Request\Transaction;

use Hyperf\Validation\Request\FormRequest;

class TransactionStoreRequest extends FormRequest
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
            'payer' => 'required|string',
            'payee' => 'required|string',
            'value' => 'required|numeric'
        ];
    }
}
