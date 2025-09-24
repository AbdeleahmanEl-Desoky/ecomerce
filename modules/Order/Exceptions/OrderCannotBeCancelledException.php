<?php

declare(strict_types=1);

namespace Modules\Order\Exceptions;

use App\Exceptions\BaseModuleException;

class OrderCannotBeCancelledException extends BaseModuleException
{
    protected string $errorType = 'ORDER_CANNOT_BE_CANCELLED';
    protected string $errorCode = 'E422_ORDER_CANCELLATION_FAILED';
    protected int $statusCode = 422;

    public function __construct(string $reason = '')
    {
        $message = 'Order cannot be cancelled';
        
        $details = [
            'reason' => $reason ?: 'Order cancellation is not allowed',
            'rules' => [
                'Orders can only be cancelled within 24 hours of creation',
                'Orders with status "completed" or "cancelled" cannot be cancelled',
                'Orders that have been shipped cannot be cancelled'
            ]
        ];

        parent::__construct($message, $details);
    }
}
