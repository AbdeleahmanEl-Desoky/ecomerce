<?php

declare(strict_types=1);

namespace Modules\Category\Services;

use Illuminate\Support\Collection;
use Modules\Category\DTO\CreateCategoryDTO;
use Modules\Category\Models\Category;
use Modules\Category\Repositories\CategoryRepository;
use Ramsey\Uuid\UuidInterface;

class CategoryCRUDService
{
    public function __construct(
        private CategoryRepository $repository,
    ) {}

    public function create(CreateCategoryDTO $createCategoryDTO): Category
    {
        return $this->repository->createCategory($createCategoryDTO->toArray());
    }

    public function list(int $page = 1, int $perPage = 10): array
    {
        return $this->repository->paginatedWithRelations(
            conditions: [],
            with: ['parent'],
            page: $page,
            perPage: $perPage,
        );
    }

    public function get(UuidInterface $id): Category
    {
        return $this->repository->getCategory(
            id: $id,
        );
    }

    /**
     * Get categories with custom relationships
     */
    public function listWithRelations(array $with = [], int $page = 1, int $perPage = 10): array
    {
        return $this->repository->paginatedWithRelations(
            conditions: [],
            with: $with,
            page: $page,
            perPage: $perPage,
        );
    }
    /**
     * Get all orders including soft deleted
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
     * Get only soft deleted orders
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
     * Restore a soft deleted order
     */
    public function restore(UuidInterface $id): bool
    {
        return $this->repository->restore($id);
    }

    /**
     * Permanently delete an order
     */
    public function forceDelete(UuidInterface $id): bool
    {
        return $this->repository->forceDelete($id);
    }
}
