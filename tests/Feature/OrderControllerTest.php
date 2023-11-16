<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIndexReturnsOrderCollection()
    {
        $orders = Order::factory(3)->create();

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);

        foreach ($orders as $order) {
            $response->assertSee($order->id);
        }
    }

    public function testStoreCreatesNewOrderAndReturnsResource()
    {
        $customer = Customer::factory()->create();
        $products = Product::factory(2)->create();

        $requestData = [
            'customer_id' => $customer->id,
            'payed' => false,
            'products' => $products->pluck('id')->all(),
        ];

        $response = $this->post('/api/orders', $requestData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'payed',
                    'customer',
                    'products',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment([
                'payed' => false,
                'customer' => $customer->full_name,
            ]);
    }

    public function testStoreFailsWithInvalidData()
    {
        $invalidRequestData = [
            'customer_id' => 'invalid',
            'payed' => 'invalid',
            'products' => 'invalid'
        ];

        $response = $this->postJson('/api/orders', $invalidRequestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testShowReturnsOrderResource()
    {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'payed',
                    'customer',
                    'products',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment([
                'id' => $order->id,
                'customer' => $order->customer->full_name,
            ]);
    }

    public function testUpdateUpdatesOrderAndReturnsResource()
    {
        $order = Order::factory()->create();
        $customer = Customer::factory()->create();
        $products = Product::factory(2)->create();

        $requestData = [
            'customer_id' => $customer->id,
            'payed' => false,
            'products' => $products->pluck('id')->all(),
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $requestData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'payed',
                    'customer',
                    'products',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment([
                'id' => $order->id,
                'customer' => $customer->full_name,
            ]);
    }

    public function testUpdateFailsWithInvalidData()
    {
        $order = Order::factory()->create();

        $invalidRequestData = [
            'customer_id' => 'invalid',
            'payed' => 'invalid',
            'products' => 'invalid'
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $invalidRequestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateFailsWithPayed()
    {
        $order = Order::factory()->create(['payed' => true]);
        $customer = Customer::factory()->create();
        $products = Product::factory(2)->create();

        $requestData = [
            'customer_id' => $customer->id,
            'payed' => false,
            'products' => $products->pluck('id')->all(),
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDestroyDeletesOrderAndReturnsJsonResponse()
    {
        $order = Order::factory()->create();

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['type' => 'success', 'message' => 'Order deleted successfully']);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function testDestroyDeletesFailsWhenOrderHasProducts()
    {
        $products = Product::factory(2)->create();
        $order = Order::factory()->create();
        $order->products()->attach($products->pluck('id')->all());

        $this->assertCount(2, $order->products);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testForceDeleteOrdersWithProducts()
    {
        $products = Product::factory(2)->create();
        $order = Order::factory()->create();
        $order->products()->attach($products->pluck('id')->all());

        $this->assertCount(2, $order->products);

        $response = $this->deleteJson("/api/orders/{$order->id}/force-delete");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['type' => 'success', 'message' => 'Order deleted successfully']);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function testAddProductsToOrder()
    {
        $order = Order::factory()->create();
        $products = Product::factory(2)->create();

        $requestData = [
            'products' => $products->pluck('id')->all(),
        ];

        $response = $this->postJson("/api/orders/{$order->id}/add-products", $requestData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['type' => 'success', 'message' => 'Products added successfully']);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => $products->pluck('id')->all(),
        ]);
    }

    public function testFailToAddProductsToOrderWhenOrderIsPayed()
    {
        $order = Order::factory()->create(['payed' => true]);
        $products = Product::factory(2)->create();

        $requestData = [
            'products' => $products->pluck('id')->all(),
        ];

        $response = $this->postJson("/api/orders/{$order->id}/add-products", $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testPayOrder()
    {
        $products = Product::factory()->create([
            'name' => 'Merc Clothing',
            'price' => 73.78
        ]);

        $order = Order::factory()->create();
        $order->products()->attach($products->pluck('id')->all());

        $response = $this->postJson("/api/orders/{$order->id}/pay");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['type' => 'success', 'message' => 'Order paid successfully']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payed' => true,
        ]);
    }

    public function testFailAlreadyPayedOrder()
    {
        $products = Product::factory()->create([
            'name' => 'Merc Clothing',
            'price' => 73.78
        ]);

        $order = Order::factory()->create(['payed' => true]);
        $order->products()->attach($products->pluck('id')->all());

        $response = $this->postJson("/api/orders/{$order->id}/pay");

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


}
