<?php

declare(strict_types=1);

namespace Modules\RateLimit\Exceptions;

use App\Exceptions\BaseModuleException;

class RateLimitExceededException extends BaseModuleException
{
    protected string $errorType = 'RATE_LIMIT_EXCEEDED';
    protected string $errorCode = 'E429_RATE_LIMIT_EXCEEDED';
    protected int $statusCode = 429;

    public function __construct(string $action = '', int $retryAfter = 60)
    {
        $message = 'Rate limit exceeded';
        
        $details = [
            'action' => $action,
            'retry_after' => $retryAfter,
            'suggestion' => "Please wait {$retryAfter} seconds before trying again",
            'info' => 'Rate limits help protect our service from abuse'
        ];

        parent::__construct($message, $details);
    }
}
