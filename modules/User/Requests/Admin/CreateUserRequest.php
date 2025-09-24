<?php

declare(strict_types=1);

namespace Modules\User\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;
use Modules\User\DTO\CreateUserDTO;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'role' => 'required|string|in:admin,customer',  
        ];
    }

    public function createCreateUserDTO(): CreateUserDTO
    {
        return new CreateUserDTO(
            name: $this->get('name'),
            email: $this->get('email'),
            password: $this->get('password'),
            role: $this->get('role'),
        );
    }
}
