<?php

declare(strict_types=1);

namespace Modules\Category\DTO;

use Ramsey\Uuid\UuidInterface;

class CreateCategoryDTO
{
    public function __construct(
        public string $name,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
