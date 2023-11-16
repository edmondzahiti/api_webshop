<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function getModel(): Order
    {
        return new Order();
    }

    public function attachProducts($order, array $productIds)
    {
        $order->products()->attach($productIds);
    }

    public function syncProducts($order, array $productIds)
    {
        $order->products()->sync($productIds);
    }
}
