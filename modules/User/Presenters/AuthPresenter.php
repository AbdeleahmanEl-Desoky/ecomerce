<?php

declare(strict_types=1);

namespace Modules\User\Presenters;

use Modules\User\Models\User;
use BasePackage\Shared\Presenters\AbstractPresenter;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthPresenter extends AbstractPresenter
{
    private User $user;
    private ?string $token;

    public function __construct(User $user, ?string $token = null)
    {
        $this->user = $user;
        $this->token = $token ?? (string) JWTAuth::fromUser($user);
    }

    protected function present(bool $isListing = false): array
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'role' => $this->user->role,
            'token' => $this->token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl', 60) * 60,
            'guard' => $this->user->role
        ];
    }
}
