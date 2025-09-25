<?php

declare(strict_types=1);

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository as RepositoriesBaseRepository;
use BasePackage\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Ramsey\Uuid\UuidInterface;
use Modules\Product\Models\Product;

/**
 * @property Product $model
 * @method Product findOneOrFail($id)
 * @method Product findOneByOrFail(array $data)
 */
class ProductRepository extends RepositoriesBaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getProductList(?int $page, ?int $perPage = 10): Collection
    {
        return $this->paginatedList([], $page, $perPage);
    }

    public function getProduct(UuidInterface $id): Product
    {
        return $this->findOneByOrFail([
            'id' => $id->toString(),
        ]);
    }

    public function createProduct(array $data): Product
    {
        return $this->create($data);
    }

    public function updateProduct(UuidInterface $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function deleteProduct(UuidInterface $id): bool
    {
        return $this->delete($id);
    }
}
