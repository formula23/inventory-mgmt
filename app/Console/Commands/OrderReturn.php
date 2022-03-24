<?php

namespace App\Console\Commands;

use App\Batch;
use App\OrderDetail;
use App\SaleOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OrderReturn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:return';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Return items on a sale order';


    protected $order;

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
        $this->info('We will walk you through a few steps to gather some information at this return...');

        $order_number = $this->ask('Sale Order Number to return?');

        if(is_null($order_number)) {
            return;
//            $order_number = 'SO319-48723';
//            $order_number = 'SO319-12408';
        }

        $this->order = SaleOrder::where('ref_number', $order_number)->with('customer')->first();

        $this->info($this->order->customer->name." (".$this->order->customer_type.")");
        $this->info("Order Date: ".$this->order->txn_date);
        $this->info("Order Total: ".display_currency($this->order->total));
        $this->info("Order Balance: ".display_currency($this->order->balance));
        $this->info('Order Items:');

        $order_details = collect();

        $this->order->order_details->each(function($item, $k) use ($order_details) {

//            if(!$item->cog) return;

            $order_details->push([
                $k,
                $item->sold_as_name,
                ($item->batch?$item->batch->batch_number:''),
                ($item->batch?$item->batch->ref_number:''),
                $item->units,
                $item->units_accepted,
                $item->order_detail_returned->sum('units_accepted'),
                ($item->units_accepted + $item->order_detail_returned->sum('units_accepted')),
                $item->unit_cost,
                $item->unit_sale_price,
            ]);

        });

        $cols = ['#','Name','Batch','UID','Ordered','Accepted','Returned','Avail Return','Cost','Price'];
        $this->table($cols, $order_details->toArray());

        if ($this->confirm('Is this the correct order you wish to return?')) {

            $items_to_return = collect();

            do {
                $line = $this->ask('Line item # to return?');

                if(is_null($line)) break;

                $original_order_detail = $this->order->order_details->get($line);
                $order_detail_to_return = $order_details->get($line);

                $quantity_to_return = $this->ask('How many '.$order_detail_to_return[1].' to return?');

                if(is_null($quantity_to_return)) {
                    $quantity_to_return = $order_detail_to_return[7];
                }

                if($quantity_to_return > $order_detail_to_return[7]) {
                    $this->error('Unable to return greater amount than avialable!');
                    continue;
                }

                $price_return = $this->ask('Return unit price: '.$order_detail_to_return[9]);

                if(is_null($price_return) || $price_return == 'yes') {
                    $price_return = $order_detail_to_return[9];
                }

                $this->info($quantity_to_return);
                $this->info($price_return);

                $return_line_data = new OrderDetail();
                $return_line_data->parent_id = $original_order_detail->id;
                $return_line_data->batch_id = $original_order_detail->batch_id;
                $return_line_data->sold_as_name = $original_order_detail->sold_as_name;

//                $item_multplier = ($original_order_detail->cog)?-1:1;

                $return_line_data->units = -$quantity_to_return;
                $return_line_data->units_accepted = -$quantity_to_return;

                $return_line_data->unit_cost = $original_order_detail->unit_cost;
                $return_line_data->unit_sale_price = $price_return;

                $return_line_data->cog = $original_order_detail->cog;

                $items_to_return->push($return_line_data);

                $cols = ['parent_id','batch_id','Name','Units','Units Accepted','Unit Cost','Unit Sale Price', 'Cog'];
                $this->table($cols, $items_to_return->toArray());

            } while( ! is_null($line));

            if( ! $items_to_return->count()) {
                $this->error('Nothing to return');
                return;
            }


            if($this->confirm('Are the items above correct to be returned?')) {

                $this->info('Create Return');

                $date = Carbon::now()->format('Y-m-d');

                $return_order = new SaleOrder();
                $return_order->parent_id = $this->order->id;
                $return_order->user_id = 13;
                $return_order->sales_rep_id = $this->order->sales_rep_id;
                $return_order->customer_id = $this->order->customer_id;
                $return_order->fund_id = $this->order->fund_id;
                $return_order->txn_date = $date;
                $return_order->type = 'return';
                $return_order->sale_type = $this->order->sale_type;
                $return_order->status = 'returned';
                $return_order->customer_type = $this->order->customer_type;
                $return_order->ref_number = $this->order->ref_number."-R".($this->order->return_orders->count()+1);
                $return_order->tax = 0;
                $return_order->transpo_tax = 0;
                $return_order->total = $return_order->subtotal = $items_to_return->sum('line_item_subtotal');
                $return_order->balance = $return_order->total;
                $return_order->save();

                $return_order->order_details()->saveMany($items_to_return);

                $return_order->applyPayment($return_order->total, $date, 'Credit', $return_order->ref_number, 'Credit memo applied to: '.$this->order->ref_number);

                //applyPayment($amount, $txn_date, $payment_method=null, $ref_number=null, $memo=null)
                $this->order->applyPayment(-($return_order->total), $date, 'Credit', $return_order->ref_number, 'Credit memo: '.$return_order->ref_number.' applied');

                //increase inventory
                $items_to_return->each(function ($item) {
                    if($item->cog) {
                        $batch = Batch::find($item->batch_id);
                        $batch->inventory -= $item->units_accepted;
                        $batch->save();
                    }
                });

            }

//dd($items_to_return->toArray());

        } else {
            $this->info('No return to process.');
        }
    }
}
