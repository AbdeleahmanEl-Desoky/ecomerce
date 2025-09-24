<?php

declare(strict_types=1);

namespace Modules\Order\Controllers;

use BasePackage\Shared\Presenters\Json;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Order\Handlers\DeleteOrderHandler;
use Modules\Order\Handlers\UpdateOrderHandler;
use Modules\Order\Presenters\OrderPresenter;
use Modules\Order\Requests\CreateOrderRequest;
use Modules\Order\Requests\DeleteOrderRequest;
use Modules\Order\Requests\GetOrderListRequest;
use Modules\Order\Requests\GetOrderRequest;
use Modules\Order\Requests\UpdateOrderRequest;
use Modules\Order\Services\OrderCRUDService;
use Ramsey\Uuid\Uuid;

class OrderController extends Controller
{
    public function __construct(
        private OrderCRUDService $orderService,
        private UpdateOrderHandler $updateOrderHandler,
        private DeleteOrderHandler $deleteOrderHandler,
    ) {
    }

    public function index(GetOrderListRequest $request): JsonResponse
    {
        $list = $this->orderService->list(
            (int) $request->get('page', 1),
            (int) $request->get('per_page', 10)
        );

        return Json::item(null,['orders' => OrderPresenter::collection($list['data']),'pagination' => $list['pagination']]);
    }

    public function show(GetOrderRequest $request): JsonResponse
    {
        $item = $this->orderService->get(Uuid::fromString($request->route('id')));

        $presenter = new OrderPresenter($item);

        return Json::item($presenter->getData());
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $createdItem = $this->orderService->create($request->createCreateOrderDTO());

        $presenter = new OrderPresenter($createdItem);

        return Json::item($presenter->getData());
    }

    public function update(UpdateOrderRequest $request): JsonResponse
    {
        $command = $request->createUpdateOrderCommand();
        $this->updateOrderHandler->handle($command);

        $item = $this->orderService->get($command->getId());

        $presenter = new OrderPresenter($item);

        return Json::item($presenter->getData());
    }

    public function delete(DeleteOrderRequest $request): JsonResponse
    {
        $this->deleteOrderHandler->handle(Uuid::fromString($request->route('id')));

        return Json::deleted();
    }
}
