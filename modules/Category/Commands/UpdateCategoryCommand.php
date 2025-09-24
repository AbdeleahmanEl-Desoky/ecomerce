<?php

declare(strict_types=1);

namespace Modules\Category\Commands;

use Ramsey\Uuid\UuidInterface;

class UpdateCategoryCommand
{
    public function __construct(
        private UuidInterface $id,
        private string $name,
        private ?string $slug = null,
        private ?string $parent_id = null,
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getParentId(): ?string
    {
        return $this->parent_id;
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
        ]);
    }
}
