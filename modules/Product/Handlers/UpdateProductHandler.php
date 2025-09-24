<?php

declare(strict_types=1);

namespace Modules\Product\Handlers;

use Modules\Product\Commands\UpdateProductCommand;
use Modules\Product\Repositories\ProductRepository;

class UpdateProductHandler
{
    public function __construct(
        private ProductRepository $repository,
    ) {
    }

    public function handle(UpdateProductCommand $updateProductCommand)
    {
        $this->repository->updateProduct($updateProductCommand->getId(), $updateProductCommand->toArray());
    }
}
