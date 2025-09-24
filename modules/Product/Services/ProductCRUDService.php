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
}
