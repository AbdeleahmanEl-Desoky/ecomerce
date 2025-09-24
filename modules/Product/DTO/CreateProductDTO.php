<?php

declare(strict_types=1);

namespace Modules\Product\DTO;

use Ramsey\Uuid\UuidInterface;

class CreateProductDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public float $price,
        public int $stock_quantity = 0,
        public string $sku,
        public string $status = 'active',
        public UuidInterface $category_id,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'sku' => $this->sku,
            'status' => $this->status,
            'category_id' => $this->category_id->toString(),
        ];
    }
}
