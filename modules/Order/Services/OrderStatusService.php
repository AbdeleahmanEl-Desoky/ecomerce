<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Modules\Order\Models\Order;
use Carbon\Carbon;

class OrderStatusService
{
    /**
     * Available order statuses
     */
    public const STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    ];

    /**
     * Status transitions that are allowed
     */
    public const ALLOWED_TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'cancelled' => []
    ];

    /**
     * Check if order can be cancelled (within 24 hours)
     */
    public function canBeCancelled(Order $order): bool
    {
        if ($order->status === 'cancelled') {
            return false;
        }
        
        // Cannot cancel if already shipped or delivered
        if (in_array($order->status, ['shipped', 'delivered'])) {
            return false;
        }
        
        return $order->created_at->diffInHours(now()) <= 24;
    }

    /**
     * Check if status transition is allowed
     */
    public function canTransitionTo(Order $order, string $newStatus): bool
    {
        if (!array_key_exists($newStatus, self::STATUSES)) {
            return false;
        }
        
        $currentStatus = $order->status;
        $allowedTransitions = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
        
        return in_array($newStatus, $allowedTransitions);
    }

    /**
     * Update order status with validation
     */
    public function updateStatus(Order $order, string $newStatus): bool
    {
        if (!$this->canTransitionTo($order, $newStatus)) {
            return false;
        }
        
        $order->status = $newStatus;
        return $order->save();
    }

    /**
     * Cancel order with business rules
     */
    public function cancelOrder(Order $order): bool
    {
        if (!$this->canBeCancelled($order)) {
            return false;
        }
        
        $order->status = 'cancelled';
        return $order->save();
    }

    /**
     * Get allowed next statuses for an order
     */
    public function getAllowedNextStatuses(Order $order): array
    {
        $currentStatus = $order->status;
        $allowedStatuses = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
        
        $result = [];
        foreach ($allowedStatuses as $status) {
            $result[$status] = self::STATUSES[$status];
        }
        
        return $result;
    }

    /**
     * Check if order is in a specific status
     */
    public function isStatus(Order $order, string $status): bool
    {
        return $order->status === $status;
    }

    /**
     * Check if order is pending
     */
    public function isPending(Order $order): bool
    {
        return $this->isStatus($order, 'pending');
    }

    /**
     * Check if order is confirmed
     */
    public function isConfirmed(Order $order): bool
    {
        return $this->isStatus($order, 'confirmed');
    }

    /**
     * Check if order is processing
     */
    public function isProcessing(Order $order): bool
    {
        return $this->isStatus($order, 'processing');
    }

    /**
     * Check if order is shipped
     */
    public function isShipped(Order $order): bool
    {
        return $this->isStatus($order, 'shipped');
    }

    /**
     * Check if order is delivered
     */
    public function isDelivered(Order $order): bool
    {
        return $this->isStatus($order, 'delivered');
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled(Order $order): bool
    {
        return $this->isStatus($order, 'cancelled');
    }

    /**
     * Check if order is completed (delivered or cancelled)
     */
    public function isCompleted(Order $order): bool
    {
        return in_array($order->status, ['delivered', 'cancelled']);
    }

    /**
     * Check if order is active (not cancelled or delivered)
     */
    public function isActive(Order $order): bool
    {
        return !$this->isCompleted($order);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName(string $status): string
    {
        return self::STATUSES[$status] ?? 'Unknown';
    }

    /**
     * Get all available statuses
     */
    public function getAllStatuses(): array
    {
        return self::STATUSES;
    }

    /**
     * Get status color for UI (optional helper)
     */
    public function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'light'
        };
    }
}
