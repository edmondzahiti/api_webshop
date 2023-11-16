<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProductsToOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Services\Order\OrderServiceInterface;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): OrderCollection
    {
        return new OrderCollection($this->orderService->all());
    }

    public function store(StoreOrderRequest $request): OrderResource
    {
        return new OrderResource($this->orderService->create($request->validated()));
    }

    public function show($id): OrderResource
    {
        return new OrderResource($this->orderService->find($id));
    }

    public function update(UpdateOrderRequest $request, $id): OrderResource
    {
        return new OrderResource($this->orderService->update($id, $request->validated()));
    }

    public function destroy($id): JsonResponse
    {
        return $this->orderService->delete($id);
    }

    public function forceDestroy($id): JsonResponse
    {
        return $this->orderService->forceDelete($id);
    }

    public function addProducts(AddProductsToOrderRequest $request, $id): JsonResponse
    {
        return $this->orderService->addProducts($id, $request->validated());
    }

    public function payOrder($id): JsonResponse
    {
        return $this->orderService->payOrder($id);
    }
}
