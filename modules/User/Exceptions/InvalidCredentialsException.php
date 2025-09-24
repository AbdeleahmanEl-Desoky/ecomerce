<?php

declare(strict_types=1);

namespace Modules\User\Exceptions;

use App\Exceptions\BaseModuleException;

class InvalidCredentialsException extends BaseModuleException
{
    protected string $errorType = 'INVALID_CREDENTIALS';
    protected string $errorCode = 'E401_INVALID_CREDENTIALS';
    protected int $statusCode = 401;

    public function __construct()
    {
        $message = 'Invalid credentials provided';
        
        $details = [
            'suggestion' => 'Please check your email and password and try again',
            'security_note' => 'Multiple failed attempts may result in account lockout'
        ];

        parent::__construct($message, $details);
    }
}
