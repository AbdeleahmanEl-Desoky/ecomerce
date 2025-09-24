<?php

declare(strict_types=1);

namespace Modules\Category\Models;

use BasePackage\Shared\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Category\Database\factories\CategoryFactory;
use BasePackage\Shared\Traits\BaseFilterable;
use Illuminate\Support\Str;
//use BasePackage\Shared\Traits\HasTranslations;

class Category extends Model
{
    use HasFactory;
    use UuidTrait;
    use BaseFilterable;
    //use HasTranslations;
    //use SoftDeletes;

    //public array $translatable = [];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (parent, grandparent, etc.)
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Check if this category is a root category (has no parent)
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this category is a leaf category (has no children)
     */
    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    /**
     * Get the depth level of this category
     */
    public function getDepth(): int
    {
        return $this->ancestors()->count();
    }

    /**
     * Scope to get only root categories
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get categories with their children
     */
    public function scopeWithChildren($query)
    {
        return $query->with('children');
    }
}
