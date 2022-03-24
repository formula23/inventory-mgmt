<?php

namespace App\Console\Commands;

use App\Batch;
use App\TransferLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class FixReconcileBatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:reconcile_batches';

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
        //get all batches to reconcile to zero

        $batches = Batch::where('category_id',35)->where('inventory','>',0)->get();

        $this->info('Count: '.$batches->count());

        $loss=0;

        $batches->each(function($batch, $idx) use (&$loss) {

            $this->info($batch->id. " - ".$batch->name. " - Inv: ".$batch->inventory."  -  ".$batch->uom);

            //create tranfser log entry

            $batch_loss = ($batch->inventory * $batch->unit_price);

            $data= [
                'user_id'=>13,
                'batch_id'=>$batch->id,
                'quantity_transferred'=>$batch->inventory,
                'start_wt_grams'=>0,
                'inventory_loss'=>$batch_loss,
                'inventory_loss_grams'=>($batch->inventory * config('highline.uom')[$batch->uom]),
                'shortage'=>0,
                'shortage_grams'=>0,
                'packer_name'=>'Reconcile',
                'type'=>'Reconcile',
                'reason'=>'Waste',
                'notes'=>'Expired Product',
            ];

            $loss += $batch_loss;

            TransferLog::create($data);

            $batch->inventory = 0;
            $batch->save();

        });

        dump('Loss Amount: '.$loss);
        dd('End');
    }
}
