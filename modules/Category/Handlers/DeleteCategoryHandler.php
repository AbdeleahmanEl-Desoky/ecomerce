<?php

declare(strict_types=1);

namespace Modules\Category\Handlers;

use Modules\Category\Repositories\CategoryRepository;
use Ramsey\Uuid\UuidInterface;

class DeleteCategoryHandler
{
    public function __construct(
        private CategoryRepository $repository,
    ) {
    }

    public function handle(UuidInterface $id)
    {
        $this->repository->deleteCategory($id);
    }
}
