<?php

declare(strict_types=1);

namespace Modules\User\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\DTO\CreateUserDTO;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|string|in:customer'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name cannot exceed 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'role.in' => 'Role must be either admin or customer'
        ];
    }

    public function createCreateUserDTO(): CreateUserDTO
    {
        return new CreateUserDTO(
            name: $this->get('name'),
            email: $this->get('email'),
            password: $this->get('password'),
            role: $this->get('role', 'customer')
        );
    }
}
