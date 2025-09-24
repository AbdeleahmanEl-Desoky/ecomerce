<?php

declare(strict_types=1);

namespace Modules\Order\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected array $stockIssues;

    public function __construct(array $stockIssues, string $message = 'Insufficient stock for one or more products')
    {
        $this->stockIssues = $stockIssues;
        parent::__construct($message);
    }

    public function getStockIssues(): array
    {
        return $this->stockIssues;
    }

    public function toArray(): array
    {
        return [
            'success' => false,
            'message' => $this->getMessage(),
            'errors' => [
                'stock_issues' => $this->stockIssues
            ]
        ];
    }
}
