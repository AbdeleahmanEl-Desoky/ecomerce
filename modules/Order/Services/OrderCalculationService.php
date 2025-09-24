<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;
use Illuminate\Support\Collection;

class OrderCalculationService
{
    /**
     * Calculate subtotal from order items
     */
    public function calculateSubtotal(Collection $orderItems): float
    {
        return $orderItems->sum(function ($item) {
            $quantity = (int) $item->quantity;
            $price = (float) $item->price_at_time;
            return $quantity * $price;
        });
    }

    /**
     * Calculate subtotal from items array (for DTOs)
     */
    public function calculateSubtotalFromArray(array $items): float
    {
        return array_sum(array_map(function ($item) {
            $quantity = (int) $item['quantity'];
            $price = (float) $item['price_at_time'];
            return $quantity * $price;
        }, $items));
    }

    /**
     * Calculate discount based on subtotal
     * Business Rule: 10% discount for orders over $100
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal > 100) {
            return round($subtotal * 0.10, 2);
        }
        
        return 0.00;
    }

    /**
     * Calculate discount with different rules (extensible)
     */
    public function calculateDiscountWithRules(float $subtotal, array $discountRules = []): float
    {
        $discount = 0.00;

        // Default rule: 10% for orders over $100
        if (empty($discountRules)) {
            return $this->calculateDiscount($subtotal);
        }

        // Apply custom discount rules
        foreach ($discountRules as $rule) {
            if ($subtotal >= $rule['minimum_amount']) {
                $ruleDiscount = 0;
                
                if ($rule['type'] === 'percentage') {
                    $ruleDiscount = round($subtotal * ($rule['value'] / 100), 2);
                } elseif ($rule['type'] === 'fixed') {
                    $ruleDiscount = $rule['value'];
                }
                
                // Take the highest discount
                $discount = max($discount, $ruleDiscount);
            }
        }

        return $discount;
    }

    /**
     * Calculate total amount (subtotal - discount)
     */
    public function calculateTotal(float $subtotal, float $discount): float
    {
        return round($subtotal - $discount, 2);
    }

    /**
     * Calculate all amounts for an order
     */
    public function calculateOrderAmounts(Collection $orderItems, array $discountRules = []): array
    {
        $subtotal = $this->calculateSubtotal($orderItems);
        $discount = $this->calculateDiscountWithRules($subtotal, $discountRules);
        $total = $this->calculateTotal($subtotal, $discount);

        return [
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'discount_percentage' => $subtotal > 0 ? round(($discount / $subtotal) * 100, 2) : 0.00,
            'has_discount' => $discount > 0,
        ];
    }

    /**
     * Calculate amounts from items array (for order creation)
     */
    public function calculateAmountsFromArray(array $items, array $discountRules = []): array
    {
        $subtotal = $this->calculateSubtotalFromArray($items);
        $discount = $this->calculateDiscountWithRules($subtotal, $discountRules);
        $total = $this->calculateTotal($subtotal, $discount);

        return [
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'discount_percentage' => $subtotal > 0 ? round(($discount / $subtotal) * 100, 2) : 0.00,
            'has_discount' => $discount > 0,
        ];
    }

    /**
     * Recalculate order totals and update the order
     */
    public function recalculateOrderTotals(Order $order, array $discountRules = []): Order
    {
        $amounts = $this->calculateOrderAmounts($order->orderItems, $discountRules);
        
        $order->subtotal_amount = $amounts['subtotal_amount'];
        $order->discount_amount = $amounts['discount_amount'];
        $order->total_amount = $amounts['total_amount'];
        
        return $order;
    }

    /**
     * Calculate item subtotal
     */
    public function calculateItemSubtotal(int $quantity, float|string $priceAtTime): float
    {
        $price = is_string($priceAtTime) ? (float) $priceAtTime : $priceAtTime;
        return round($quantity * $price, 2);
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentage(float $subtotal, float $discount): float
    {
        if ($subtotal > 0) {
            return round(($discount / $subtotal) * 100, 2);
        }
        
        return 0.00;
    }

    /**
     * Validate discount rules format
     */
    public function validateDiscountRules(array $discountRules): bool
    {
        foreach ($discountRules as $rule) {
            if (!isset($rule['minimum_amount'], $rule['type'], $rule['value'])) {
                return false;
            }
            
            if (!in_array($rule['type'], ['percentage', 'fixed'])) {
                return false;
            }
            
            if (!is_numeric($rule['minimum_amount']) || !is_numeric($rule['value'])) {
                return false;
            }
        }
        
        return true;
    }
}
