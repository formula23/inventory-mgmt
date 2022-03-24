<?php

namespace App\Console\Commands;

use App\Batch;
use App\License;
use App\LicenseType;
use App\PurchaseOrder;
use App\SaleOrder;
use App\TransferLog;
use App\User;
use App\Vendor;
use Illuminate\Console\Command;

class FixStuff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:stuff';

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

        // fix sales orders

        $batches = Batch::where('units_purchased', 0)->with('transfer_log_details')->get();

        foreach($batches as $batch) {

            if($batch->transfer_log_details->count()) {

                $this->info($batch->id);
                $batch->units_purchased = $batch->transfer_log_details->sum('units');
                $batch->save();

                $this->info($batch->units_purchased);
                $this->info('Saved!');

            }

        }


    }


}
