<?php

use App\Batch;
use App\PurchaseOrder;
use Illuminate\Database\Seeder;

class OrderBatchTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PurchaseOrder::whereNull('fund_id')->update(['fund_id'=>1]);
        Batch::whereNull('fund_id')->update(['fund_id'=>1]);
    }
}
