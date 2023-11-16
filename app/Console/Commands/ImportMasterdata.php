<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportMasterdata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-masterdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('import-products');
        $this->call('import-customers');
    }
}
