<?php

declare(strict_types=1);

namespace Modules\Product\Presenters;

use Modules\Product\Models\Product;
use BasePackage\Shared\Presenters\AbstractPresenter;
use Modules\Category\Presenters\CategoryPresenter;

class ProductPresenter extends AbstractPresenter
{
    private Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    protected function present(bool $isListing = false): array
    {
        return [
            'id' => $this->product->id,
            'name' => $this->product->name, 
            'description' => $this->product->description,
            'price'=> $this->product->price,
            'stock_quantity'=> $this->product->stock_quantity,
            'sku'=> $this->product->sku,
            'status'=> $this->product->status,
            'category'=> $this->product->category ? (new CategoryPresenter($this->product->category))->getData() : null,
        ];
    }
}
