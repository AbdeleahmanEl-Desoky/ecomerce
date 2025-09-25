<?php

declare(strict_types=1);

namespace Modules\Order\Models;

use BasePackage\Shared\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use BasePackage\Shared\Traits\BaseFilterable;
use Modules\Product\Models\Product;
use Modules\Order\Services\OrderCalculationService;

class OrderItem extends Model
{
    use HasFactory;
    use UuidTrait;
    use BaseFilterable;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_at_time',
    ];

    protected $casts = [
        'id' => 'string',
        'quantity' => 'integer',
        'price_at_time' => 'decimal:2',
    ];

    /**
     * Get the order that owns the order item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this order item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate the subtotal for this item
     */
    public function getSubtotalAttribute(): float
    {
        $calculationService = app(OrderCalculationService::class);
        return $calculationService->calculateItemSubtotal($this->quantity, (float) $this->price_at_time);
    }

    /**
     * Get the subtotal (alias for getSubtotalAttribute)
     */
    public function subtotal(): float
    {
        return $this->getSubtotalAttribute();
    }
}
