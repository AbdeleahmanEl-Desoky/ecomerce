<?php

declare(strict_types=1);

namespace Modules\User\Services\Customer;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\User\DTO\CreateUserDTO;
use Modules\User\Models\User;
use Modules\User\Repositories\UserRepository;
use Ramsey\Uuid\UuidInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserCustomerService
{
    public function __construct(
        private UserRepository $repository,
    ) {
    }

    public function create(CreateUserDTO $createUserDTO): User
    {
        return $user = $this->repository->createUser($createUserDTO->toArray());        
    }

    public function login(array $credentials): ?User
    {
        try {
            $guard = 'customer';
            if (Auth::guard($guard)->attempt($credentials)) {
                $user = Auth::guard($guard)->user();
                return $user instanceof User ? $user : null;
            }
            
            return null;
        } catch (JWTException $e) {
            throw new \Exception('Could not create token: ' . $e->getMessage());
        }         
    }
    public function list(int $page = 1, int $perPage = 10): array
    {
        return $this->repository->paginated(
            page: $page,
            perPage: $perPage,
        );
    }

    public function get(UuidInterface $id): User
    {
        return $this->repository->getUser(
            id: $id,
        );
    }
}
