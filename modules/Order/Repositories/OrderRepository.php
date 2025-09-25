<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Ramsey\Uuid\UuidInterface;
use Modules\Order\Models\Order;

/**
 * @property Order $model
 * @method Order findOneOrFail($id)
 * @method Order findOneByOrFail(array $data)
 */
class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getOrderList(?int $page, ?int $perPage = 10): Collection
    {
        return $this->paginatedList([], $page, $perPage);
    }

    public function getOrder(UuidInterface $id): Order
    {
        return $this->findOneByOrFail([
            'id' => $id->toString(),
        ]);
    }

    public function createOrder(array $data): Order
    {
        return $this->create($data);
    }

    public function updateOrder(UuidInterface $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function deleteOrder(UuidInterface $id): bool
    {
        return $this->delete($id);
    }
}
