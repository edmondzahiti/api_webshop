<?php

namespace Tests\Feature;

use App\Console\Commands\ImportCustomers;
use App\Console\Commands\ImportProducts;
use App\Imports\CustomersImport;
use App\Imports\ProductsImport;
use App\Models\Customer;
use App\Models\Product;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testImportProductsCommand()
    {
        $importMock = $this->getMockBuilder(ProductsImport::class)
            ->onlyMethods(['model', 'onFailure', 'rules', 'prepareData'])
            ->setConstructorArgs([$this->createMock(ProductRepositoryInterface::class), $this->createMock(ImportProducts::class)])
            ->getMock();
        $importMock->expects($this->any())->method('model')->willReturn(null);
        $importMock->expects($this->any())->method('onFailure');

        $this->app->bind(ProductsImport::class, function () use ($importMock) {
            return $importMock;
        });

        $this->artisan('import-products')
            ->expectsOutput('Importing Products...')
            ->expectsOutput('Products import completed')
            ->assertExitCode(0);

        $this->assertGreaterThan(0, Product::count());
    }

    public function testImportCustomersCommand()
    {
        $importMock = $this->getMockBuilder(CustomersImport::class)
            ->onlyMethods(['model', 'onFailure', 'rules', 'prepareData'])
            ->setConstructorArgs([$this->createMock(CustomerRepositoryInterface::class), $this->createMock(ImportCustomers::class)])
            ->getMock();
        $importMock->expects($this->any())->method('model')->willReturn(null);
        $importMock->expects($this->any())->method('onFailure');

        $this->app->bind(CustomersImport::class, function () use ($importMock) {
            return $importMock;
        });

        $this->artisan('import-customers')
            ->expectsOutput('Importing Customers...')
            ->expectsOutput('Customers import completed')
            ->assertExitCode(0);

        $this->assertGreaterThan(0, Customer::count());
    }
}
