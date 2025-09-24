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
    ) {
    }

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
}
