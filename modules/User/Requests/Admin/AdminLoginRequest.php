<?php

declare(strict_types=1);

namespace Modules\User\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
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
            'email.required' => 'Admin email is required',
            'email.email' => 'Please provide a valid admin email address',
            'password.required' => 'Admin password is required',
            'password.string' => 'Password must be a string'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'admin email',
            'password' => 'admin password'
        ];
    }
}
