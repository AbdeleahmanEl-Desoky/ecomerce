<?php

declare(strict_types=1);

namespace App\Repositories;

use BasePackage\Shared\Repositories\BaseRepository as VendorBaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Ramsey\Uuid\UuidInterface;

abstract class BaseRepository extends VendorBaseRepository
{

    /**
     * Get paginated results including soft deleted
     */
    public function paginatedWithTrashed(array $conditions = [], int $page = 1, int $perPage = 10, array $with = []): array
    {
        $query = $this->model->withTrashed();
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        if (method_exists($this->model, 'scopeFilter')) {
            $query = $query->filter(request()->all())->where($conditions);
        } else {
            $query = $query->where($conditions);
        }
        
        $total = $query->count();
        $items = $query->skip(($page - 1) * $perPage)
                      ->take($perPage)
                      ->get();
        
        return [
            'data' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ]
        ];
    }

    /**
     * Get paginated soft deleted results only
     */
    public function paginatedOnlyTrashed(array $conditions = [], int $page = 1, int $perPage = 10, array $with = []): array
    {
        $query = $this->model->onlyTrashed();
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        if (method_exists($this->model, 'scopeFilter')) {
            $query->filter(request()->all())->where($conditions);
        } else {
            $query->where($conditions);
        }
        
        $total = $query->count();
        $items = $query->skip(($page - 1) * $perPage)
                      ->take($perPage)
                      ->get();
        
        return [
            'data' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ]
        ];
    }

    /**
     * Find by ID including trashed
     */
    public function findWithTrashed(UuidInterface $id)
    {
        return $this->model->withTrashed()->find($id);
    }

    /**
     * Restore soft deleted record
     */
    public function restore(UuidInterface $id): bool
    {
        $model = $this->model->withTrashed()->findOrFail($id);
        return $model->restore();
    }

    /**
     * Force delete record permanently
     */
    public function forceDelete(UuidInterface $id): bool
    {
        $model = $this->model->withTrashed()->findOrFail($id);
        return $model->forceDelete();
    }

}
