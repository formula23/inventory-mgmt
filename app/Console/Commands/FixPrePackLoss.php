<?php

namespace App\Console\Commands;

use App\TransferLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixPrePackLoss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:prepackloss {--save}';

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

        $this->check_transfers();

//        $this->check_shortage();

        return;



//        $transfer_logs = TransferLog::with(['batch_converted','transfer_log_details.batch_created'])
//            ->where('type','Pre-Pack')
//            ->where('inventory_loss',0)
////            ->where('id','>=',897)
//            ->whereDate('created_at','>=','2018-12-01')
//            ->get();
//
//        $total_cost_loss = 0;
//
//        foreach($transfer_logs as $transfer_log)
//        {
//
//            $source_batch = $transfer_log->batch_converted;
//
//            $this->info($transfer_log->id);
//            $this->info('Source batch: '. $source_batch->name);
//
////            dump($source_batch);
//
//            $unit_price = $source_batch->unit_price;
//            $this->info( $unit_price);
//
//            $gram_price = $source_batch->unit_price / config('highline.uom.'.$source_batch->uom);
//            $this->info('gram price: '.$gram_price);
//
//            $transfer_log_details = $transfer_log->transfer_log_details;
//
//            $grams_created = 0;
//            $total_cost_created=0;
//            foreach($transfer_log_details as $transfer_log_detail)
//            {
//                $cost_created = $transfer_log_detail->batch_created->unit_price * $transfer_log_detail->units;
//                $total_cost_created+=$cost_created;
//
//                $grams = config('highline.uom.'.$transfer_log_detail->batch_created->uom);
//                $grams_created += $transfer_log_detail->units * $grams;
//
//                $this->info("created ".$transfer_log_detail->batch_created->uom.": ".$cost_created);
//
//            }
//
//            $this->info('total cost: '.$total_cost_created);
//
//
//            $total_source_cost = ($source_batch->unit_price * $transfer_log->quantity_transferred);
//            $total_source_grams = config('highline.uom.'.$source_batch->uom) * $transfer_log->quantity_transferred;
//
//            $cost_loss = round($total_source_cost - $total_cost_created, 2);
//            $gram_loss = round($total_source_grams - $grams_created, 2);
//
//            $total_cost_loss += $cost_loss;
//
//            $transfer_log->inventory_loss = $cost_loss;
//            $transfer_log->inventory_loss_grams = $gram_loss;
//            $transfer_log->save();
//
//            $this->info('gram loss: '.$gram_loss);
//            $this->info('cost loss: '.$cost_loss);
//            $this->info('----');
//
////            dd($created_batches);
//        }

//        $this->info('Total loss: '. $total_cost_loss);

//        dd($transfer_logs->first()->batch_converted->top_level_parent);

    }

    private function check_transfers()
    {
//        \DB::enableQueryLog();

        $transfer_logs_qry = TransferLog::with(['batch_converted','transfer_log_details.batch_created'])
//            ->where('type','Pre-Pack')
//            ->where('inventory_loss',0)
//            ->where('id','>=',897)
            ->whereDate('created_at','>=','2019-02-01')
//            ->whereDate('created_at','<=','2019-02-28')
        ;

        $transfer_logs = $transfer_logs_qry->get();
//        dump(\DB::getQueryLog());
//        dd($transfer_logs->toArray());

        $total_loss_diff=0;
        $total_shortage_diff=0;
        $total_stored_loss=0;
        $total_stored_shortage=0;
        $actual_loss=0;
        $actual_shortage=0;

        foreach($transfer_logs as $transfer_log)
        {
//            if( ! in_array($transfer_log->id, [1889,1890,1891])) continue;

            try{
                $cost_transferred = abs($transfer_log->quantity_transferred) * $transfer_log->batch_converted->unit_price;

                try{
                    $gram_price = $transfer_log->batch_converted->unit_price / config('highline.uom.'.$transfer_log->batch_converted->uom);
                } catch(\Exception $e)
                {
                    $this->error($transfer_log->id);
                    $this->error($e->getMessage());
                }


                //get shortage amount
                $calc_shortage = round($gram_price * $transfer_log->shortage_grams, 2);

                $adjusted_cost_transferred = $cost_transferred - $calc_shortage;

            } catch(\Exception $e) {
                $this->error($e->getMessage());
                continue;
            }

            if($transfer_log->transfer_log_details->count()) {

                $cost_created = 0;
                foreach ($transfer_log->transfer_log_details as $transfer_log_detail) {
//                    $this->info($transfer_log_detail->action);

                    $created_cost = round($transfer_log_detail->units * $transfer_log_detail->batch_created->unit_price,2);
//                    $this->info($transfer_log_detail->units.' units @ '.$transfer_log_detail->batch_created->unit_price." = ".$created_cost);

                    $cost_created += $created_cost;

                }

                $loss = (float)round($adjusted_cost_transferred - $cost_created, 2);
                $stored_loss = (float)($transfer_log->inventory_loss);
                $total_stored_loss += $stored_loss;
                $total_stored_shortage += $transfer_log->shortage;

                $actual_loss += $loss;
                $actual_shortage += $calc_shortage;

                if (abs($loss - $stored_loss) > 0.001 || abs($calc_shortage - $transfer_log->shortage) > 0.001) {

                    $this->info('-------------------------------------');
                    $this->info('transfer log id:'. $transfer_log->id);

                    $this->info('batch id: ' . $transfer_log->batch_id . "--" . $transfer_log->batch_converted->name);
                    $this->info('transferred qty: ' . abs($transfer_log->quantity_transferred) . " - " . $transfer_log->batch_converted->unit_price);

                    $this->info('cost xfer: ' . $adjusted_cost_transferred);
                    $this->info('total cost created:' . $cost_created);

                    $this->info('actually loss: ' . $loss);
                    $this->info('stored inventory loss:' . $transfer_log->inventory_loss);

                    $this->info('actually shortage: ' . $calc_shortage);
                    $this->info('stored shortage:' . $transfer_log->shortage);

//                    $this->info('stored shortage:' . $transfer_log->shortage);

                    $loss_diff = round($loss - $stored_loss, 2);
                    $total_loss_diff += $loss_diff;

                    $shortage_diff = round($calc_shortage - $transfer_log->shortage, 2);
                    $total_shortage_diff += $shortage_diff;

                    $this->info('loss diff: '.$loss_diff);
                    $this->info('shortage diff: '.$shortage_diff);
                    $this->error('ERROR');

                    //save the loss
                    if($this->option('save')) {
                        $transfer_log->inventory_loss = $loss;
                        $transfer_log->shortage = $calc_shortage;
                        $transfer_log->save();
                        $this->info('Saved!');
                    }

                }

            } else {

                //check inventory loss
                $current_loss = round($transfer_log->quantity_transferred * $transfer_log->batch_converted->unit_price, 2);

                if($current_loss != $transfer_log->inventory_loss) {

                    $this->info('-------------------------------------');
                    $this->info('transfer log id:'. $transfer_log->id);

                    $this->info('actual loss: '.$current_loss);
                    $this->info('stored loss: '.$transfer_log->inventory_loss);

                    $stored_loss = $transfer_log->inventory_loss;

                    $loss_diff = round($current_loss - $stored_loss, 2);

                    $this->info('loss diff: '.$loss_diff);

                    $total_loss_diff += $loss_diff;

                    //save the loss
                    if($this->option('save')) {
                        $transfer_log->inventory_loss = $current_loss;
                        $transfer_log->save();
                        $this->info('Saved!');
                    }

                }

                $total_stored_loss += (float)$transfer_log->inventory_loss;
                $actual_loss += $current_loss;
            }
//                dd($transfer_log->transfer_log_details);

//            dump($transfer_log);

        }


        $this->info('---- END ----');
        $this->info('total loss diff: '.$total_loss_diff);
        $this->info('total short diff: '.$total_shortage_diff);

        $this->info('actual loss: '.$actual_loss);
        $this->info('stored loss: '.$total_stored_loss);

        $this->info('actual shortage: '.$actual_shortage);
        $this->info('stored shortage: '.$total_stored_shortage);

        $this->info('actual total loss: '.($actual_loss + $actual_shortage));
        $this->info('stored total loss: '.($total_stored_loss + $total_stored_shortage));

        $this->info('total loss diff: '.(($actual_loss + $actual_shortage) - ($total_stored_loss + $total_stored_shortage)));
    }

    public function check_shortage()
    {

        $transfer_logs = TransferLog::with(['batch_converted','transfer_log_details.batch_created'])
//            ->where('type','Pre-Pack')
            ->where('shortage', '!=',0)
//            ->where('id','>=',897)
//            ->whereDate('created_at','>=','2019-02-01')

            ->get();


        foreach($transfer_logs as $transfer_log)
        {
            $this->info('transfer id:'.$transfer_log->id);
            $this->info('short grams:'. $transfer_log->shortage_grams);
            $this->info('short amount:'. $transfer_log->shortage);

            $gram_price = $transfer_log->batch_converted->unit_price / config('highline.uom.'.$transfer_log->batch_converted->uom);

            $this->info('gram price: '.$gram_price);

            $shortage_amount = round($transfer_log->shortage_grams * $gram_price, 2);
            $this->info($shortage_amount);

            $transfer_log->shortage =$shortage_amount;
            $transfer_log->save();


//            dump($transfer_log->toArray());
            $this->info('---------');
        }

    }


}
