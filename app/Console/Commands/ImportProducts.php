<?php

namespace App\Console\Commands;

use App\Imports\ProductsImport;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportProducts extends Command
{
    protected $signature = 'import-products';
    protected $description = 'Import products from CSV file';

    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        parent::__construct();
        $this->productRepository = $productRepository;
    }

    public function handle()
    {
        try {
            $this->info("Importing Products...");

            $file = storage_path('app/excel/products.csv');
            Excel::import(new ProductsImport($this->productRepository, $this), $file);

            $this->info('Products import completed');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
