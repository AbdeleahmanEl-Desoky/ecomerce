<?php

declare(strict_types=1);

namespace Modules\Category\Handlers;

use Modules\Category\Commands\UpdateCategoryCommand;
use Modules\Category\Repositories\CategoryRepository;

class UpdateCategoryHandler
{
    public function __construct(
        private CategoryRepository $repository,
    ) {
    }

    public function handle(UpdateCategoryCommand $updateCategoryCommand)
    {
        $this->repository->updateCategory($updateCategoryCommand->getId(), $updateCategoryCommand->toArray());
    }
}
