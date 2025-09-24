<?php

declare(strict_types=1);

namespace Modules\Order\Controllers\Customer;

use BasePackage\Shared\Presenters\Json;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Order\Handlers\DeleteOrderHandler;
use Modules\Order\Handlers\UpdateOrderHandler;
use Modules\Order\Presenters\OrderPresenter;
use Modules\Order\Requests\Customer\CreateOrderRequest;
use Modules\Order\Requests\CancelOrderRequest;
use Modules\Order\Requests\DeleteOrderRequest;
use Modules\Order\Requests\GetOrderListRequest;
use Modules\Order\Requests\GetOrderRequest;
use Modules\Order\Requests\UpdateOrderRequest;
use Modules\Order\Services\OrderCRUDService;
use Modules\Order\Exceptions\InsufficientStockException;
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
            (int) $request->get('per_page', 10),
            ['user_id'=> auth('customer')->user()->id],
        );

        return Json::item(OrderPresenter::collection($list['data']),$list['pagination']);
    }

    public function show(GetOrderRequest $request): JsonResponse
    {
        $item = $this->orderService->get(Uuid::fromString($request->route('id')));

        $presenter = new OrderPresenter($item);

        return Json::item($presenter->getData());
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $createdItem = $this->orderService->create($request->createCreateOrderDTO());

            $presenter = new OrderPresenter($createdItem);

            return Json::item($presenter->getData());
        } catch (InsufficientStockException $e) {
            return Json::error('Insufficient stock for one or more items: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            return Json::error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateOrderRequest $request): JsonResponse
    {
        $command = $request->createUpdateOrderCommand();
        $this->updateOrderHandler->handle($command);

        $item = $this->orderService->get($command->getId());

        $presenter = new OrderPresenter($item);

        return Json::item($presenter->getData());
    }

    public function cancel(CancelOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->get(Uuid::fromString($request->route('id')));
            
            $cancelled = $this->orderService->cancelOrder($order);
            
            if (!$cancelled) {
                return Json::error('Order cannot be cancelled. Orders can only be cancelled within 24 hours of creation.', 400);
            }
            
            // Refresh the order to get updated data
            $order->refresh();
            $presenter = new OrderPresenter($order);
            
            return Json::item($presenter->getData());
        } catch (\Exception $e) {
            return Json::error('Failed to cancel order: ' . $e->getMessage(), 500);
        }
    }
    /**
     * Get stock status for dashboard
     */
    public function stockStatus(): JsonResponse
    {
        $stockStatus = $this->orderService->getStockStatus();
        
        return Json::item($stockStatus);
    }
}
