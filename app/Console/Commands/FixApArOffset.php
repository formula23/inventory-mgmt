<?php

namespace App\Console\Commands;

use App\PurchaseOrder;
use App\SaleOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixApArOffset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:ap_ar_offset';

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

        //get all open SOs to offset with POs

        $customer_id = 34; // Cannary
        $vendor_id = 34; //Cannary

        $sale_orders = SaleOrder::where('status','delivered')
            ->where('customer_id', $customer_id)
            ->where('balance','>',0)
            ->orderBy('txn_date', 'asc')
            ->get();

        $purchase_orders = PurchaseOrder::where('vendor_id', $vendor_id)
            ->where('balance','>',0)
            ->orderBy('txn_date', 'asc')
            ->get();


        $this->info('Purchase order balance: '.display_currency($purchase_orders->sum('balance')));
        $this->info('Sale order balance: '.display_currency($sale_orders->sum('balance')));

        $total_offset_amount = min($purchase_orders->sum('balance'), $sale_orders->sum('balance'));

        $total_offset_amount_bal = $total_offset_amount;

        if( ! $total_offset_amount_bal) {
            $this->error('Exiting - Nothing to offset');
            return;
        }

        $this->info('Total offset amount: '.display_currency($total_offset_amount_bal));

        $memo = "$150,219.76 AP/AR offset";

        foreach($sale_orders as $sale_order) {

            $this->info($sale_order->txn_date->format('m/d/Y'));
            $this->info($sale_order->ref_number);

            $amount = ($total_offset_amount_bal < $sale_order->balance ? $total_offset_amount_bal : $sale_order->balance);

            $this->info('Amount: '.display_currency($amount));

            $total_offset_amount_bal -= $amount;

            $sale_order->applyPayment($amount, Carbon::today(), 'Credit', null, $memo);

            if(!$total_offset_amount_bal) {
                break;
            }

            $this->info('------------');
        }

        $this->info("***************************************************");
        $this->info("***************************************************");
        $this->info("***************************************************");

        $total_offset_amount_bal = $total_offset_amount;
        //Apply offset amount to POs

        foreach($purchase_orders as $purchase_order) {

            $this->info($purchase_order->ref_number);
            $this->info($purchase_order->balance);

            $amount = ($total_offset_amount_bal < $purchase_order->balance ? $total_offset_amount_bal : $purchase_order->balance);

            $total_offset_amount_bal -= $amount;

            $this->info('Rec balance: '. $total_offset_amount_bal);
            $this->info('Amount: '.$amount);

            $purchase_order->applyPayment($amount, Carbon::today(), 'Credit', null, $memo);

            if(!$total_offset_amount_bal) {
                break;
            }

            $this->info('------------');
        }

        $this->info("END");
    }
}
