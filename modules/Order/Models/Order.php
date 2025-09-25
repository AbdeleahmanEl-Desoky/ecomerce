<?php

declare(strict_types=1);

namespace Modules\Order\Models;

use BasePackage\Shared\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\Database\factories\OrderFactory;
use BasePackage\Shared\Traits\BaseFilterable;
use Modules\User\Models\User;
use Modules\Order\Services\OrderNumberService;
use Carbon\Carbon;
//use BasePackage\Shared\Traits\HasTranslations;

class Order extends Model
{
    use HasFactory;
    use UuidTrait;
    use BaseFilterable;
    use SoftDeletes;
    //use HasTranslations;

    //public array $translatable = [];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal_amount',
        'discount_amount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'id' => 'string',
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the order items with products
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->with('product');
    }

    /**
     * Get the total items count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->orderItems->sum('quantity');
    }

    /**
     * Get the items count (number of different products)
     */
    public function getItemsCountAttribute(): int
    {
        return $this->orderItems->count();
    }

    /**
     * Scope to get orders by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get orders by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get recent orders
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get discount percentage attribute
     */
    public function getDiscountPercentageAttribute(): float
    {
        if ($this->subtotal_amount > 0) {
            return round(($this->discount_amount / $this->subtotal_amount) * 100, 2);
        }
        
        return 0.00;
    }

    /**
     * Get has discount attribute
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_amount > 0;
    }
}
