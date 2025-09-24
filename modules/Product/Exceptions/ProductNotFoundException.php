<?php

declare(strict_types=1);

namespace Modules\Product\Exceptions;

use App\Exceptions\BaseModuleException;

class ProductNotFoundException extends BaseModuleException
{
    protected string $errorType = 'PRODUCT_NOT_FOUND';
    protected string $errorCode = 'E404_PRODUCT_NOT_FOUND';
    protected int $statusCode = 404;

    public function __construct(string $productId = '')
    {
        $message = $productId 
            ? "Product with ID '{$productId}' not found"
            : 'Product not found';

        $details = [
            'product_id' => $productId,
            'suggestion' => 'Please check the product ID and try again'
        ];

        parent::__construct($message, $details);
    }
}
