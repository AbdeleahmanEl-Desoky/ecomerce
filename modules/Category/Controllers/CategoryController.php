<?php

declare(strict_types=1);

namespace Modules\Category\Controllers;

use BasePackage\Shared\Presenters\Json;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Category\Handlers\DeleteCategoryHandler;
use Modules\Category\Handlers\UpdateCategoryHandler;
use Modules\Category\Presenters\CategoryPresenter;
use Modules\Category\Requests\CreateCategoryRequest;
use Modules\Category\Requests\DeleteCategoryRequest;
use Modules\Category\Requests\GetCategoryListRequest;
use Modules\Category\Requests\GetCategoryRequest;
use Modules\Category\Requests\UpdateCategoryRequest;
use Modules\Category\Services\CategoryCRUDService;
use Ramsey\Uuid\Uuid;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryCRUDService $categoryService,
        private UpdateCategoryHandler $updateCategoryHandler,
        private DeleteCategoryHandler $deleteCategoryHandler,
    ) {
    }

    public function index(GetCategoryListRequest $request): JsonResponse
    {
        $list = $this->categoryService->list(
            (int) $request->get('page', 1),
            (int) $request->get('per_page', 10)
        );

        return Json::item(CategoryPresenter::collection($list['data']),$list['pagination']);
    }

    public function show(GetCategoryRequest $request): JsonResponse
    {
        $item = $this->categoryService->get(Uuid::fromString($request->route('id')));

        $presenter = new CategoryPresenter($item);

        return Json::item($presenter->getData());
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $createdItem = $this->categoryService->create($request->createCreateCategoryDTO());

        $presenter = new CategoryPresenter($createdItem);

        return Json::item($presenter->getData());
    }

    public function update(UpdateCategoryRequest $request): JsonResponse
    {
        $command = $request->createUpdateCategoryCommand();
        $this->updateCategoryHandler->handle($command);

        $item = $this->categoryService->get($command->getId());

        $presenter = new CategoryPresenter($item);

        return Json::item($presenter->getData());
    }

    public function delete(DeleteCategoryRequest $request): JsonResponse
    {
        $this->deleteCategoryHandler->handle(Uuid::fromString($request->route('id')));

        return Json::deleted();
    }
}
