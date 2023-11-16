<?php

namespace App\Repositories\Order;

interface OrderRepositoryInterface
{
    public function get();

    public function create(array $data);

    public function find($id, $relations = []);

    public function findOrFail($id, $relations = []);

    public function update($model, array $data);

    public function delete($model);

    public function attachProducts($order, array $productIds);

    public function syncProducts($order, array $productIds);
}
