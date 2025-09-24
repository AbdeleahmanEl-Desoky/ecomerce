<?php

declare(strict_types=1);

namespace Modules\User\DTO;

use Ramsey\Uuid\UuidInterface;

class CreateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ];
    }
}
