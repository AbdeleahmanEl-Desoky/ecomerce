<?php

declare(strict_types=1);

namespace Modules\Order\Handlers;

use Modules\Order\Commands\UpdateOrderCommand;
use Modules\Order\Repositories\OrderRepository;

class UpdateOrderHandler
{
    public function __construct(
        private OrderRepository $repository,
    ) {
    }

    public function handle(UpdateOrderCommand $updateOrderCommand)
    {
        $this->repository->updateOrder($updateOrderCommand->getId(), $updateOrderCommand->toArray());
    }
}
