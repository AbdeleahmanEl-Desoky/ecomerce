<?php

declare(strict_types=1);

namespace Modules\Product\Commands;

use Ramsey\Uuid\UuidInterface;

class UpdateProductCommand
{
    public function __construct(
        private UuidInterface $id,
        private string $name,
        private ?string $description = null,
        private float $price,
        private int $stock_quantity = 0,
        private string $sku,
        private string $status = 'active',
        private UuidInterface $category_id,
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

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'sku' => $this->sku,
            'status' => $this->status,
            'category_id' => $this->category_id->toString(),
        ], fn($value) => $value !== null);
    }
}
