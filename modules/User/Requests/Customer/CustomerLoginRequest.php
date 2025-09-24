<?php

declare(strict_types=1);

namespace Modules\User\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerLoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Customer email is required',
            'email.email' => 'Please provide a valid customer email address',
            'password.required' => 'Customer password is required',
            'password.string' => 'Password must be a string'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'customer email',
            'password' => 'customer password'
        ];
    }
}
