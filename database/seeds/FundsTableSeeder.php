<?php

use App\Batch;
use App\Fund;
use App\Order;
use App\PurchaseOrder;
use Illuminate\Database\Seeder;

class FundsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); // disable foreign key constraints

        Fund::truncate();

        Fund::create(['name'=>'Primary']);
//        Fund::create(['name'=>'C-Fund']);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); // enable foreign key constraints
    }
}
