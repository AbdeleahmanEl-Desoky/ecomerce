<?php

declare(strict_types=1);

namespace Modules\Order\Handlers;

use Modules\Order\Repositories\OrderRepository;
use Ramsey\Uuid\UuidInterface;

class DeleteOrderHandler
{
    public function __construct(
        private OrderRepository $repository,
    ) {
    }

    public function handle(UuidInterface $id)
    {
        $this->repository->deleteOrder($id);
    }
}
