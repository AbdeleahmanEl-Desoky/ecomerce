<?php

declare(strict_types=1);

namespace Modules\Category\DTO;

use Ramsey\Uuid\UuidInterface;

class CreateCategoryDTO
{
    public function __construct(
        public string $name,
        public ?string $slug = null,
        public ?string $parent_id = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
        ];
    }
}
