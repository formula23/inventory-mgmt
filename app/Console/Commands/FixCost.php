<?php

namespace App\Console\Commands;

use App\Batch;
use App\PurchaseOrder;
use App\SaleOrder;
use Illuminate\Console\Command;

class FixCost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:cost {--keep_markup : Maintain the markup on batches that are sold}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix cost of a batch on a given PO. Optionally, maintain the markup on batches are on SO\'s';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->new_cost=[];
        $this->keep_markup = false;
        $this->so_ids = collect();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // update yolo POs
        $this->info('Start: ');

        $this->info((new Batch)->totalInventoryValue()->total_inventory_value - (new Batch)->derivedInventoryValue());

//        return false;
//        $pos = PurchaseOrder::whereIn('vendor_id', [600, 601])->with('batches.children_batches')->get();

        $pos = PurchaseOrder::where('id', 5185)->with('batches.children_batches')->get();

        $this->keep_markup = $this->option('keep_markup');

//        $this->price_reduction = 0.66;

        $this->new_cost = [
            16644=>(750+161.28),
            16645=>(850+161.28),
            16646=>(850+161.28),
            16647=>(850+161.28),
            16648=>(850+161.28),
            16649=>(750+161.28),
            16650=>(650+161.28),
        ];

        foreach ($pos as $po) {

            $this->loopBatches($po->batches, null, 0, null);

            $po->in_qb = 0;
            $po->updateTotals();

            $this->info("PO updated: " . $po->ref_number);

        }

        foreach($this->so_ids->unique() as $so_id) {

            $so = SaleOrder::where('id', $so_id)->with('transactions')->first();

            $so->calculateTotals();
            $so->in_qb = 0;
            $so->save();

            $this->info("SO Id: ".$so_id." - Changed");
        }

        $this->info('End: ');
        $this->info((new Batch)->totalInventoryValue()->total_inventory_value - (new Batch)->derivedInventoryValue());

    }

    protected function loopBatches($batches, $batch_new_price, $idx, $top_parent)
    {
        foreach ($batches as $batch) {

            if($idx==0) {
                $top_parent = $batch;

                if(count($this->new_cost) && empty($this->new_cost[$batch->id])) continue;

                if( ! empty($this->new_cost[$batch->id])) {
                    $batch_new_price = $this->new_cost[$batch->id];
                } else {
                    $batch_new_price = $batch->unit_price - $this->price_reduction;
                }
            }

            if($top_parent->uom != $batch->uom) {
                $batch_new_price = $batch_new_price * config('highline.uom')[$batch->uom];
            }

            $price_change = $batch->unit_price - $batch_new_price;

//            $this->info('pirce change: '.$price_change);

            if($idx==0) {
                $this->info("****".$batch->name);
            }
            $this->info($idx."..".$batch->id."--".$batch_new_price);

            $batch->unit_price = $batch_new_price;
            $batch->subtotal_price = ($batch->unit_price * $batch->units_purchased);
            $batch->tax = $batch->calculateCultTax();
            $batch->save();

            $this->updateOrderDetails($batch);

            if ($batch->children_batches->count()) {
                $this->loopBatches($batch->children_batches, $batch_new_price, $idx + 1, $top_parent);
            }

//            if ($batch->created_batch) {
//
//                $this->info($idx."..".$batch->created_batch->id."--".$batch_new_price);
//
//                $batch->created_batch->unit_price = $batch_new_price;
//                $batch->created_batch->subtotal_price = ($batch->created_batch->unit_price * $batch->created_batch->units_purchased);
//                $batch->created_batch->tax = $batch->created_batch->cult_tax_amount();
//                $batch->created_batch->save();
//
//                $this->updateOrderDetails($batch->created_batch);
//
//                if ($batch->created_batch->children_batches->count()) {
//                    $this->info("created.".$batch->units_purchased);
//                    $this->loopBatches($batch->created_batch->children_batches, $batch_new_price,$idx + 1, $top_parent);
//                }
//
//            }

        }
    }

    protected function updateOrderDetails($batch)
    {
        if ($batch->order_details->count()) {

            foreach ($batch->order_details as $order_detail) {

                if($this->keep_markup) {
                    $markup = $order_detail->unit_sale_price -$order_detail->unit_cost;
                    $order_detail->unit_sale_price = $batch->unit_price + $markup;
                }

                $order_detail->unit_cost = $batch->unit_price;
                $order_detail->save();

                $this->so_ids->push($order_detail->sale_order_id);

            }

        }
    }

}
