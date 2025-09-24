<?php

declare(strict_types=1);

namespace Modules\Order\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;
use Modules\Order\DTO\CreateOrderDTO;
use Modules\Order\DTO\OrderItemDTO;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id'=> 'required|uuid|exists:users,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_at_time' => 'required|numeric|min:0',
        ];
    }

    public function createCreateOrderDTO(): CreateOrderDTO
    {
        $items = [];
        foreach ($this->get('items') as $item) {
            $items[] = new OrderItemDTO(
                product_id: Uuid::fromString($item['product_id']),
                quantity: (int) $item['quantity'],
                price_at_time: (float) $item['price_at_time'],
            );
        }

        return new CreateOrderDTO(
            user_id: Uuid::fromString($this->get('user_id')),
            items: $items,
            notes: $this->get('notes'),
        );
    }
}
