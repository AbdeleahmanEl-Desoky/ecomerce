<?php

declare(strict_types=1);

namespace Modules\Order\DTO;

use Ramsey\Uuid\UuidInterface;

class OrderItemDTO
{
    public function __construct(
        public UuidInterface $product_id,
        public int $quantity,
        public float $price_at_time,
    ) {
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id->toString(),
            'quantity' => $this->quantity,
            'price_at_time' => $this->price_at_time,
        ];
    }

    public function subtotal(): float
    {
        return $this->quantity * $this->price_at_time;
    }
}
