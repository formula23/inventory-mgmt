<?php

namespace App\Console\Commands;

use App\SaleOrder;
use App\SalesCommission;
use Illuminate\Console\Command;

class FixSalesComm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:sales_comm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply sales commissions';

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
        $ids = collect([126,237,316,554,]);

        $orders = SaleOrder::whereIn('id', $ids)->get();

        $orders->each(function($val, $idx) {

            $sales_comm = SalesCommission::create([
                'user_id'=>13,
//                'sales_rep_id'=>16,
                'period_start'=>'2018-08-31',
                'period_end'=>'2018-08-31',
                'total_revenue'=>$val->total,
                'total_commission'=>0,
            ]);

            $sales_comm->sales_commission_details()->create([
                'sales_rep_id'=>16,
                'sale_order_id'=>$val->id,
                'rate'=>0,
                'amount'=>0,
            ]);

            $this->info('Order:'.$val->id);

        });


    }
}
