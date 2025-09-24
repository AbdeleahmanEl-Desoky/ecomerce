<?php

declare(strict_types=1);

namespace Modules\Product\Handlers;

use Modules\Product\Repositories\ProductRepository;
use Ramsey\Uuid\UuidInterface;

class DeleteProductHandler
{
    public function __construct(
        private ProductRepository $repository,
    ) {
    }

    public function handle(UuidInterface $id)
    {
        $this->repository->deleteProduct($id);
    }
}
