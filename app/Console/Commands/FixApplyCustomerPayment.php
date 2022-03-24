<?php

namespace App\Console\Commands;

use App\Customer;
use App\SaleOrder;
use App\User;
use Illuminate\Console\Command;

class FixApplyCustomerPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:apply_customer_payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $customer_id = 0;
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

        $customer_id = $this->ask('Customer Id?');

        $customer = Customer::withBalance()->where('users.id',$customer_id)->first();
//dd($customer->name);
        $this->info("Customer: ".$customer->name);
        $this->info("Balance: ".display_currency($customer->outstanding_balance));

        if($this->confirm('Correct?')) {

            $payment_amount = $this->ask('Payment amount?');

            if($this->confirm('Amount to apply: '.display_currency($payment_amount))) {

                $total_amount_to_apply = $payment_amount;

                $this->info('Total amount to apply: ' . $total_amount_to_apply);

                $orders = SaleOrder::where('status', 'delivered')
                    ->where('balance', '!=', 0)
                    ->where('customer_id', $customer_id)
                    ->orderBy('txn_date')
                    ->get();

                $total = 0;

                foreach ($orders as $order) {
                    if ($total_amount_to_apply == 0) break;

                    $this->info($order->ref_number);

                    $amount_to_apply = ($total_amount_to_apply > $order->balance ? $order->balance : $total_amount_to_apply);
                    $this->info("SO Balance:".$order->balance);
                    $this->info("Amount to apply:".$amount_to_apply."\n\n");

//                    $this->info('amount to apply: ' . $amount_to_apply);

                    $total_amount_to_apply -= $amount_to_apply;

//                    $this->info('Total amount balance: ' . $total_amount_to_apply."\n\n");

                    $order->applyPayment($amount_to_apply, now(), 'Credit', null, 'Applied in QB');

                    $total += $amount_to_apply;

                }

                $this->info($total_amount_to_apply);

                $this->info($total);
            }
        }


    }
}
