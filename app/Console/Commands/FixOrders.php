<?php

namespace App\Console\Commands;

use App\Batch;
use App\Order;
use App\SaleOrder;
use App\TransferLog;
use Illuminate\Console\Command;

class FixOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the excise tax on internal orders';

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

        //create transfer order
        $this->create_transfer_order();
        return;


        $transfer_logs = TransferLog::whereDate('created_at','2019-04-01')->with('transfer_log_details')->get();

        $transfer_logs->each(function ($item, $idx) {

            $this->info($item->created_at);

            $item->created_at = $item->created_at->subDay(1);

            $this->info($item->created_at);

            if($item->transfer_log_details->count()) {
                $item->transfer_log_details->each(function($item1) {
                    $item1->created_at = $item1->created_at->subDay(1);
                    $item1->save();
                });
            }

            $item->save();
            $this->info('---');
        });

//        dd($transfer_logs->toArray());

//        $orders = SaleOrder::where('customer_id', 95)->get(); //the pottery
//
//        foreach($orders as $order) {
//
//            if($order->customer_type == 'retailer' && $order->total == $order->balance) {
//
//                $order->customer_type = 'retailer nonarms length';
//                $order->tax = 0;
//                $order->total = $order->subtotal;
//                $order->balance = $order->total;
//
//                $order->save();
//
//                $this->info($order->id.' updated!');
//
//            }
//
//        }

    }

    protected function create_transfer_order()
    {
        $all_batches = Batch::where('inventory','>',0)->get();

        $this->info($all_batches->count());

        $order = SaleOrder::find(2120);

        $all_batches->each(function($batch) use ($order) {

            $order->addUpdateItem($batch, $batch->name, $batch->inventory, $batch->unit_price);

            $batch->inventory -= $batch->inventory;

            $batch->save();
            $order->save();
//            dd($batch);
        });


    }


}
