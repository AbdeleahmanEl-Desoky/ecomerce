<?php

declare(strict_types=1);

namespace Modules\Product\Filters;

use BasePackage\Shared\Filters\SearchModelFilter;

class ProductFilter extends SearchModelFilter
{
    public $relations = ['category'];

    /**
     * Filter by product name
     */
    public function name($name)
    {
        return $this->where('name', 'like', "%{$name}%");
    }

    /**
     * Filter by product description
     */
    public function description($description)
    {
        return $this->where('description', 'like', "%{$description}%");
    }

    /**
     * Filter by exact price
     */
    public function price($price)
    {
        return $this->where('price', $price);
    }

    /**
     * Filter by minimum price
     */
    public function minPrice($minPrice)
    {
        return $this->where('price', '>=', $minPrice);
    }

    /**
     * Filter by maximum price
     */
    public function maxPrice($maxPrice)
    {
        return $this->where('price', '<=', $maxPrice);
    }

    /**
     * Filter by price range
     */
    public function priceRange($priceRange)
    {
        if (is_array($priceRange) && count($priceRange) === 2) {
            return $this->whereBetween('price', $priceRange);
        }
        return $this;
    }

    /**
     * Filter by stock quantity
     */
    public function stockQuantity($stockQuantity)
    {
        return $this->where('stock_quantity', $stockQuantity);
    }

    /**
     * Filter by minimum stock quantity
     */
    public function minStock($minStock)
    {
        return $this->where('stock_quantity', '>=', $minStock);
    }

    /**
     * Filter by maximum stock quantity
     */
    public function maxStock($maxStock)
    {
        return $this->where('stock_quantity', '<=', $maxStock);
    }

    /**
     * Filter products in stock
     */
    public function inStock($inStock = true)
    {
        if ($inStock) {
            return $this->where('stock_quantity', '>', 0);
        }
        return $this->where('stock_quantity', '<=', 0);
    }

    /**
     * Filter by SKU
     */
    public function sku($sku)
    {
        return $this->where('sku', 'like', "%{$sku}%");
    }

    /**
     * Filter by status
     */
    public function status($status)
    {
        return $this->where('status', $status);
    }

    /**
     * Global search across name, description, and SKU
     */
    public function search($search)
    {
        return $this->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    /**
     * Filter by created date range
     */
    public function createdBetween($dateRange)
    {
        if (is_array($dateRange) && count($dateRange) === 2) {
            return $this->whereBetween('created_at', $dateRange);
        }
        return $this;
    }

    /**
     * Filter by updated date range
     */
    public function updatedBetween($dateRange)
    {
        if (is_array($dateRange) && count($dateRange) === 2) {
            return $this->whereBetween('updated_at', $dateRange);
        }
        return $this;
    }

    /**
     * Filter by category ID
     */
    public function category($categoryId)
    {
        return $this->where('category_id', $categoryId);
    }

    /**
     * Filter by category name
     */
    public function categoryName($categoryName)
    {
        return $this->whereHas('category', function ($query) use ($categoryName) {
            $query->where('name', 'like', "%{$categoryName}%");
        });
    }

    /**
     * Filter by category slug
     */
    public function categorySlug($categorySlug)
    {
        return $this->whereHas('category', function ($query) use ($categorySlug) {
            $query->where('slug', $categorySlug);
        });
    }

    /**
     * Filter by multiple category IDs
     */
    public function categoryIds($categoryIds)
    {
        if (is_array($categoryIds)) {
            return $this->whereIn('category_id', $categoryIds);
        }
        return $this;
    }
}
