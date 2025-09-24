<?php

declare(strict_types=1);

namespace Modules\Category\Repositories;

use BasePackage\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Ramsey\Uuid\UuidInterface;
use Modules\Category\Models\Category;

/**
 * @property Category $model
 * @method Category findOneOrFail($id)
 * @method Category findOneByOrFail(array $data)
 */
class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getCategoryList(?int $page, ?int $perPage = 10): Collection
    {
        return $this->paginatedList([], $page, $perPage);
    }

    public function getCategory(UuidInterface $id): Category
    {
        return $this->findOneByOrFail([
            'id' => $id->toString(),
        ]);
    }

    public function createCategory(array $data): Category
    {
        return $this->create($data);
    }

    public function updateCategory(UuidInterface $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function deleteCategory(UuidInterface $id): bool
    {
        return $this->delete($id);
    }
}
