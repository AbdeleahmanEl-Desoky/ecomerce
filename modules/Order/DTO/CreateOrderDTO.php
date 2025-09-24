<?php

declare(strict_types=1);

namespace Modules\Order\DTO;

use Ramsey\Uuid\UuidInterface;

class CreateOrderDTO
{
    public function __construct(
        public UuidInterface $user_id,
        public array $items, // Array of OrderItemDTO
        public ?string $notes = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id->toString(),
            'notes' => $this->notes,
            'items' => array_map(fn($item) => $item->toArray(), $this->items),
        ];
    }

    public function calculateTotal(): float
    {
        return array_sum(array_map(fn($item) => $item->subtotal(), $this->items));
    }
}
