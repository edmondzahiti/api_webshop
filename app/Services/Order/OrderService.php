<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Traits\PaymentTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderService implements OrderServiceInterface
{
    use PaymentTrait;

    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function all(): Collection
    {
        return $this->orderRepository->get();
    }

    public function find($id): Order
    {
        return $this->orderRepository->findOrFail($id);
    }

    public function create($data): Order
    {
        $order = $this->orderRepository->create($data);

        $this->orderRepository->attachProducts($order, $data['products'] ?? []);

        return $order;
    }

    public function update($id, $data): Order
    {
        $order = $this->orderRepository->findOrFail($id);
        $this->checkOrderNotPaid($order);

        $this->orderRepository->update($order, $data);

        $this->orderRepository->syncProducts($order, $data['products'] ?? []);

        return $order->refresh();
    }

    public function delete($id): JsonResponse
    {
        $order = $this->orderRepository->findOrFail($id);

        if ($order->products()->count() > 0) {
            throw new UnprocessableEntityHttpException('Order has associated products');
        }

        $this->orderRepository->delete($order);

        return response()->json(['type' => 'success', 'message' => 'Order deleted successfully']);
    }

    public function forceDelete($id): JsonResponse
    {
        $order = $this->orderRepository->findOrFail($id);

        $this->orderRepository->delete($order);

        return response()->json(['type' => 'success', 'message' => 'Order deleted successfully']);
    }

    public function addProducts($id, $data): JsonResponse
    {
        $order = $this->orderRepository->findOrFail($id);
        $this->checkOrderNotPaid($order);

        $this->orderRepository->attachProducts($order, $data['products']);

        return response()->json(['type' => 'success', 'message' => 'Products added successfully']);
    }

    public function payOrder($id): JsonResponse
    {
        $order = $this->orderRepository->findOrFail($id);
        $this->checkOrderNotPaid($order);

        $baseUrl = Config::get('superpayment.base_url');

        $this->makePaymentRequest($baseUrl . '/pay', [
            'order_id' => $order->id,
            'customer_email' => $order->customer->email,
            'value' => $order->calculateTotal(),
        ]);

        $this->orderRepository->update($order, ['payed' => true]);

        return response()->json(['type' => 'success', 'message' => 'Order paid successfully']);
    }

    public function checkOrderNotPaid($order)
    {
        if ($order->isPaid()) {
            throw new UnprocessableEntityHttpException('Order is already paid');
        }
    }

}
