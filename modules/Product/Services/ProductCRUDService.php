<?php

declare(strict_types=1);

namespace Modules\Product\Services;

use Illuminate\Support\Collection;
use Modules\Product\DTO\CreateProductDTO;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\ProductRepository;
use Ramsey\Uuid\UuidInterface;

class ProductCRUDService
{
    public function __construct(
        private ProductRepository $repository,
    ) {
    }

    public function create(CreateProductDTO $createProductDTO): Product
    {
         return $this->repository->createProduct($createProductDTO->toArray());
    }

    public function list(int $page = 1, int $perPage = 10): array
    {
        return $this->repository->paginated(
            page: $page,
            perPage: $perPage,
        );
    }

    public function get(UuidInterface $id): Product
    {
        return $this->repository->getProduct(
            id: $id,
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
