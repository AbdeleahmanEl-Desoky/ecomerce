<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Modules\Product\Models\Product;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Exception;

class StockManagementService
{
    /**
     * Check if products have sufficient stock for order
     */
    public function checkStockAvailability(array $items): array
    {
        $stockIssues = [];
        
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                $stockIssues[] = [
                    'product_id' => $item['product_id'],
                    'message' => 'Product not found'
                ];
                continue;
            }
            
            if ($product->stock_quantity < $item['quantity']) {
                $stockIssues[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'requested_quantity' => $item['quantity'],
                    'available_quantity' => $product->stock_quantity,
                    'message' => "Insufficient stock. Requested: {$item['quantity']}, Available: {$product->stock_quantity}"
                ];
            }
        }
        
        return $stockIssues;
    }
    
    /**
     * Reserve stock for order items
     */
    public function reserveStock(array $items): bool
    {
        return DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                
                if (!$product) {
                    throw new Exception("Product {$item['product_id']} not found");
                }
                
                if ($product->stock_quantity < $item['quantity']) {
                    throw new Exception("Insufficient stock for product {$product->name}. Available: {$product->stock_quantity}, Requested: {$item['quantity']}");
                }
                
                // Decrease stock
                $product->stock_quantity -= $item['quantity'];
                $product->save();
            }
            
            return true;
        });
    }
    
    /**
     * Release stock when order is cancelled
     */
    public function releaseStock(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $orderItem) {
                $product = Product::lockForUpdate()->find($orderItem->product_id);
                
                if ($product) {
                    // Increase stock back
                    $product->stock_quantity += $orderItem->quantity;
                    $product->save();
                }
            }
            
            return true;
        });
    }
    
    /**
     * Update stock when order quantity is modified
     */
    public function updateStock(OrderItem $orderItem, int $newQuantity): bool
    {
        return DB::transaction(function () use ($orderItem, $newQuantity) {
            $product = Product::lockForUpdate()->find($orderItem->product_id);
            
            if (!$product) {
                throw new Exception("Product not found");
            }
            
            $quantityDifference = $newQuantity - $orderItem->quantity;
            
            if ($quantityDifference > 0) {
                // Need more stock
                if ($product->stock_quantity < $quantityDifference) {
                    throw new Exception("Insufficient stock for increase. Available: {$product->stock_quantity}, Needed: {$quantityDifference}");
                }
                $product->stock_quantity -= $quantityDifference;
            } else {
                // Releasing stock
                $product->stock_quantity += abs($quantityDifference);
            }
            
            $product->save();
            $orderItem->quantity = $newQuantity;
            $orderItem->save();
            
            return true;
        });
    }
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts(int $threshold = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('stock_quantity', '<=', $threshold)
            ->where('status', 'active')
            ->orderBy('stock_quantity', 'asc')
            ->get();
    }
    
    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('stock_quantity', '<=', 0)
            ->where('status', 'active')
            ->get();
    }
}
