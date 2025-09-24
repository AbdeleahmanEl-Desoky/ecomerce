<?php

declare(strict_types=1);

namespace Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;
use Modules\User\DTO\CreateUserDTO;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

    public function createCreateUserDTO(): CreateUserDTO
    {
        return new CreateUserDTO(
            name: $this->get('name'),
        );
    }
}
