<?php

namespace App\Services\Order;


use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

interface OrderServiceInterface
{
    public function all(): Collection;

    public function find($id): Order;

    public function create(array $data): Order;

    public function update($model, array $data): Order;

    public function delete($model): JsonResponse;

    public function forceDelete($model): JsonResponse;

    public function addProducts($model, $data): JsonResponse;

    public function payOrder($order): JsonResponse;
}
