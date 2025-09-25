<?php

declare(strict_types=1);

namespace Modules\Category\Repositories;

use App\Repositories\BaseRepository as RepositoriesBaseRepository;
use BasePackage\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Ramsey\Uuid\UuidInterface;
use Modules\Category\Models\Category;

/**
 * @property Category $model
 * @method Category findOneOrFail($id)
 * @method Category findOneByOrFail(array $data)
 */
class CategoryRepository extends RepositoriesBaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
    public function paginatedWithRelations(
        array $conditions = [],
        array $with = [],
        int $page = 1,
        int $perPage = 15,
        string $orderBy = 'created_at',
        string $sortBy = 'desc'
    ) {
        if (method_exists($this->model, 'scopeFilter')) {
            $query = $this->model->filter(request()->all())->where($conditions);
        } else {
            $query = $this->model->where($conditions);
        }

        $query->with($with);

        $count = $query->count();
        $paginatedData = $query->forPage($page, $perPage)->orderBy($orderBy, $sortBy)->get();
        $paginationArray = $this->getPaginationInformation($page, $perPage, $count);

        return [
            'pagination' => $paginationArray['pagination'],
            'data' => $paginatedData,
        ];
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
