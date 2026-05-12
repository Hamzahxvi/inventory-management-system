<?php

namespace App\Console\Commands;

use App\Jobs\CheckLowStock;
use Illuminate\Console\Command;

class DispatchLowStockCheck extends Command
{
    protected $signature = 'inventory:check-low-stock';
    protected $description = 'Dispatch a background job to check for low stock products';

    public function handle(): int
    {
        CheckLowStock::dispatch();
        $this->info('Low stock check dispatched to queue.');

        return self::SUCCESS;
    }
}
