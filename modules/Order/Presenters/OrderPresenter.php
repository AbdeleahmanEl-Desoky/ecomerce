<?php

declare(strict_types=1);

namespace Modules\Order\Presenters;

use Modules\Order\Models\Order;
use BasePackage\Shared\Presenters\AbstractPresenter;

class OrderPresenter extends AbstractPresenter
{
    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    protected function present(bool $isListing = false): array
    {
        return [
            'id' => $this->order->id,
            'name' => $this->order->name,
        ];
    }
}
