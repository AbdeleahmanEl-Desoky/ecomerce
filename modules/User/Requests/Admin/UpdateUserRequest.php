<?php

declare(strict_types=1);

namespace Modules\User\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;
use Modules\User\Commands\UpdateUserCommand;
use Modules\User\Handlers\UpdateUserHandler;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth('admin')->user()->id . ',id',
        ];
    }

    public function createUpdateUserCommand(): UpdateUserCommand
    {
        return new UpdateUserCommand(
            id: Uuid::fromString(auth('admin')->user()->id),
            name: $this->get('name'),
            email: $this->get('email'),
        );
    }
}
