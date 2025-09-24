<?php

declare(strict_types=1);

namespace Modules\User\Exceptions;

use App\Exceptions\BaseModuleException;

class UserNotFoundException extends BaseModuleException
{
    protected string $errorType = 'USER_NOT_FOUND';
    protected string $errorCode = 'E404_USER_NOT_FOUND';
    protected int $statusCode = 404;

    public function __construct(string $identifier = '')
    {
        $message = $identifier 
            ? "User with identifier '{$identifier}' not found"
            : 'User not found';

        $details = [
            'identifier' => $identifier,
            'suggestion' => 'Please check the user ID or email and try again'
        ];

        parent::__construct($message, $details);
    }
}
