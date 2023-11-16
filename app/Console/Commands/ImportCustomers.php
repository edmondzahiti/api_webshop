<?php

namespace App\Console\Commands;

use App\Imports\CustomersImport;
use App\Repositories\Customer\CustomerRepositoryInterface;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportCustomers extends Command
{
    protected $signature = 'import-customers';
    protected $description = 'Import customers from CSV file';

    protected $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        parent::__construct();
        $this->customerRepository = $customerRepository;
    }

    public function handle()
    {
        try {
            $this->info("Importing Customers...");

            $file = storage_path('app/excel/customers.csv');
            Excel::import(new CustomersImport($this->customerRepository, $this), $file);

            $this->info('Customers import completed');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
