<?php

declare(strict_types=1);

namespace Modules\User\Services\Admin;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\User\DTO\CreateUserDTO;
use Modules\User\Models\User;
use Modules\User\Repositories\UserRepository;
use Ramsey\Uuid\UuidInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserCRUDService
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
            $guard = 'admin';
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

    /**
     * Soft delete a user
     */
    public function delete(UuidInterface $id): bool
    {
        $user = $this->get($id);
        return $user->delete();
    }

    /**
     * Get all users including soft deleted
     */
    public function listWithTrashed(int $page = 1, int $perPage = 10): array
    {
        return $this->repository->paginatedWithTrashed(
            conditions: [],
            page: $page,
            perPage: $perPage,
        );
    }

    /**
     * Get only soft deleted users
     */
    public function listOnlyTrashed(int $page = 1, int $perPage = 10): array
    {
        return $this->repository->paginatedOnlyTrashed(
            conditions: [],
            page: $page,
            perPage: $perPage,
        );
    }

    /**
     * Restore a soft deleted user
     */
    public function restore(UuidInterface $id): bool
    {
        return $this->repository->restoreUser($id);
    }

    /**
     * Permanently delete a user
     */
    public function forceDelete(UuidInterface $id): bool
    {
        return $this->repository->forceDeleteUser($id);
    }
}
