<?php

declare(strict_types=1);

namespace Modules\Category\Controllers\Customer;

use BasePackage\Shared\Presenters\Json;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Category\Presenters\CategoryPresenter;
use Modules\Category\Requests\GetCategoryListRequest;
use Modules\Category\Requests\GetCategoryRequest;
use Modules\Category\Services\CategoryCRUDService;
use Ramsey\Uuid\Uuid;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryCRUDService $categoryService,
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

}
