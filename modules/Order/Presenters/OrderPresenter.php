<?php

declare(strict_types=1);

namespace Modules\Order\Presenters;

use Modules\Order\Models\Order;
use BasePackage\Shared\Presenters\AbstractPresenter;

class OrderPresenter extends AbstractPresenter
{
    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    protected function present(bool $isListing = false): array
    {
        $data = [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'user_id' => $this->order->user_id,
            'status' => $this->order->status,
            'subtotal_amount' => $this->order->subtotal_amount,
            'discount_amount' => $this->order->discount_amount,
            'total_amount' => $this->order->total_amount,
            'discount_percentage' => $this->order->discount_percentage,
            'has_discount' => $this->order->has_discount,
            'notes' => $this->order->notes,
            'created_at' => $this->order->created_at,
            'updated_at' => $this->order->updated_at,
        ];

        // Add detailed information for single order view
        if (!$isListing) {
            $data['user'] = $this->order->user ? [
                'id' => $this->order->user->id,
                'name' => $this->order->user->name,
                'email' => $this->order->user->email,
            ] : null;

            $data['order_items'] = $this->order->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price_at_time' => $item->price_at_time,
                    'subtotal' => $item->subtotal,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'sku' => $item->product->sku,
                        'current_price' => $item->product->price,
                    ] : null,
                ];
            })->toArray();

            $data['total_items'] = $this->order->total_items;
            $data['items_count'] = $this->order->items_count;
        } else {
            // For listing, show summary information
            $data['total_items'] = $this->order->total_items;
            $data['items_count'] = $this->order->items_count;
        }

        return $data;
    }
}
