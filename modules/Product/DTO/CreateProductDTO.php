<?php

declare(strict_types=1);

namespace Modules\Product\DTO;

use Ramsey\Uuid\UuidInterface;

class CreateProductDTO
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
