<?php

namespace App\Console\Commands;

use App\SaleOrder;
use App\VaultLog;
use Illuminate\Console\Command;

class OrdersDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:order_delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $order_id = $this->ask('Order Id?');

        if($order_id) {

            $order = SaleOrder::where('id', $order_id)->with('order_details.batch')->first();

            $order->order_details->each(function ($order_detail) use ($order) {

                $order->removeItem($order_detail);

            });

            $this->info($order->id." - Delete!");
            $order->delete();

        }

    }
}
