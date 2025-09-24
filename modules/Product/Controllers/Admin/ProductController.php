<?php

declare(strict_types=1);

namespace Modules\Product\Controllers\Admin;

use BasePackage\Shared\Presenters\Json;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Handlers\DeleteProductHandler;
use Modules\Product\Handlers\UpdateProductHandler;
use Modules\Product\Presenters\ProductPresenter;
use Modules\Product\Requests\CreateProductRequest;
use Modules\Product\Requests\DeleteProductRequest;
use Modules\Product\Requests\GetProductListRequest;
use Modules\Product\Requests\GetProductRequest;
use Modules\Product\Requests\UpdateProductRequest;
use Modules\Product\Services\ProductCRUDService;
use Ramsey\Uuid\Uuid;

class ProductController extends Controller
{
    public function __construct(
        private ProductCRUDService $productService,
        private UpdateProductHandler $updateProductHandler,
        private DeleteProductHandler $deleteProductHandler,
    ) {
    }

    public function index(GetProductListRequest $request): JsonResponse
    {
        $list = $this->productService->list(
            (int) $request->get('page', 1),
            (int) $request->get('per_page', 10)
        );

        return Json::item(ProductPresenter::collection($list['data']),$list['pagination']);
    }

    public function show(GetProductRequest $request): JsonResponse
    {
        $item = $this->productService->get(Uuid::fromString($request->route('id')));

        $presenter = new ProductPresenter($item);

        return Json::item($presenter->getData());
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $createdItem = $this->productService->create($request->createCreateProductDTO());

        $presenter = new ProductPresenter($createdItem);

        return Json::item($presenter->getData());
    }

    public function update(UpdateProductRequest $request): JsonResponse
    {
        $command = $request->createUpdateProductCommand();
        $this->updateProductHandler->handle($command);

        $item = $this->productService->get($command->getId());

        $presenter = new ProductPresenter($item);

        return Json::item($presenter->getData());
    }

    public function delete(DeleteProductRequest $request): JsonResponse
    {
        $this->deleteProductHandler->handle(Uuid::fromString($request->route('id')));

        return Json::deleted();
    }
}
