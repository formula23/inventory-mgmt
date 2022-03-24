<?php

namespace App\Console\Commands;

use App\PurchaseOrder;
use App\Vendor;
use Illuminate\Console\Command;

class FixApplyVendorPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:apply_vendor_payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $vendor_id = 0;
    protected $payment_amount = 0;
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
        $vendor_id = $this->ask('Vendor Id?');

        $vendor = Vendor::withBalance()->where('users.id', $vendor_id)->first();

//        dd($vendor);

        $this->info("Vendor: ".$vendor->name);
        $this->info("Balance: ".display_currency($vendor->outstanding_balance));

        if($this->confirm('Correct?')) {

            $payment_amount = $this->ask('Payment amount?');

            switch ($this->ask('Transaction: 1 (Cash), 2 (Credit), 3 (ACH/Wire)'))
            {
                case "1":
                    $txn_type ="Cash";
                    break;
                case "2":
                    $txn_type ="Credit";
                    break;
                case "3":
                    $txn_type = "Wire";
                    break;
            }

            $ref_number = $this->ask('Reference# ?');

            $memo = $this->ask('Memo?');

            if($this->confirm('Amount to apply: '.display_currency($payment_amount))) {

                $total_amount_to_apply = $payment_amount;

                $this->info('Total amount to apply: ' . $total_amount_to_apply);

                $orders = PurchaseOrder::where('status','open')
                    ->where('balance', '!=', 0)
                    ->where('vendor_id', $vendor_id)
                    ->orderBy('txn_date')
                    ->get();

//                $orders = SaleOrder::where('status', 'delivered')
//dd($orders);
                $total = 0;

                foreach ($orders as $order) {
                    if ($total_amount_to_apply == 0) break;

                    $this->info($order->ref_number);

                    $amount_to_apply = ($total_amount_to_apply > $order->balance ? $order->balance : $total_amount_to_apply);
                    $this->info("PO Balance:".$order->balance);
                    $this->info("Amount to apply:".display_currency($amount_to_apply)."\n\n");

//                    $this->info('amount to apply: ' . $amount_to_apply);

                    $total_amount_to_apply -= $amount_to_apply;

//                    $this->info('Total amount balance: ' . $total_amount_to_apply."\n\n");

                    $order->applyPayment($amount_to_apply, now(), $txn_type, $ref_number, $memo);

                    $total += $amount_to_apply;

                }

                $this->info($total_amount_to_apply);

                $this->info($total);
            }
        }
    }
}
