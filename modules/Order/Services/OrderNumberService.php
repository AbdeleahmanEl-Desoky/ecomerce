<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Modules\Order\Models\Order;
use Carbon\Carbon;

class OrderNumberService
{
    /**
     * Generate unique order number with format ORD-YYYYMMDD-XXXX
     */
    public function generateOrderNumber(?Carbon $date = null): string
    {
        $date = $date ?? now();
        $dateString = $date->format('Ymd');
        $prefix = 'ORD-' . $dateString . '-';
        
        // Get the last order number for the specified date
        $lastOrder = Order::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();
        
        if ($lastOrder) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastOrder->order_number, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $prefix . str_pad((string) $newSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate order number with custom format
     */
    public function generateCustomOrderNumber(string $prefix = 'ORD', string $dateFormat = 'Ymd', int $sequenceLength = 4): string
    {
        $dateString = now()->format($dateFormat);
        $fullPrefix = $prefix . '-' . $dateString . '-';
        
        // Get the last order number with this prefix
        $lastOrder = Order::where('order_number', 'like', $fullPrefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();
        
        if ($lastOrder) {
            $lastSequence = (int) substr($lastOrder->order_number, -$sequenceLength);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $fullPrefix . str_pad((string) $newSequence, $sequenceLength, '0', STR_PAD_LEFT);
    }

    /**
     * Parse order number to extract components
     */
    public function parseOrderNumber(string $orderNumber): array
    {
        $parts = explode('-', $orderNumber);
        
        if (count($parts) !== 3) {
            return [
                'prefix' => null,
                'date' => null,
                'sequence' => null,
                'valid' => false
            ];
        }
        
        return [
            'prefix' => $parts[0],
            'date' => $parts[1],
            'sequence' => (int) $parts[2],
            'valid' => true
        ];
    }

    /**
     * Validate order number format
     */
    public function validateOrderNumberFormat(string $orderNumber): bool
    {
        $pattern = '/^ORD-\d{8}-\d{4}$/';
        return preg_match($pattern, $orderNumber) === 1;
    }

    /**
     * Get next sequence number for a date
     */
    public function getNextSequenceNumber(?Carbon $date = null): int
    {
        $date = $date ?? now();
        $dateString = $date->format('Ymd');
        $prefix = 'ORD-' . $dateString . '-';
        
        $lastOrder = Order::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();
        
        if ($lastOrder) {
            $lastSequence = (int) substr($lastOrder->order_number, -4);
            return $lastSequence + 1;
        }
        
        return 1;
    }

    /**
     * Get orders count for a specific date
     */
    public function getOrdersCountForDate(Carbon $date): int
    {
        $dateString = $date->format('Ymd');
        $prefix = 'ORD-' . $dateString . '-';
        
        return Order::where('order_number', 'like', $prefix . '%')->count();
    }

    /**
     * Check if order number exists
     */
    public function orderNumberExists(string $orderNumber): bool
    {
        return Order::where('order_number', $orderNumber)->exists();
    }
}
