<?php

declare(strict_types=1);

namespace Modules\Product\Presenters;

use Modules\Product\Models\Product;
use BasePackage\Shared\Presenters\AbstractPresenter;

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
        ];
    }
}
