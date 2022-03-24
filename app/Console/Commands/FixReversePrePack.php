<?php

namespace App\Console\Commands;

use App\TransferLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixReversePrePack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:reverse_prepack {transfer_log_id?}';

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
//        if(\App::environment('production')) {
//            $this->error('Disabled in Production!');
//            return;
//        }

        $transfer_log_id = $this->argument('transfer_log_id');

        if(empty($transfer_log_id)) {
            $transfer_log_id = $this->ask('Transfer Log Id?');
        }

        $transfer_log = TransferLog::with(['batch_converted', 'transfer_log_details.batch_created'])
            ->where('Type', 'Pre-Pack')
            ->findOrFail($transfer_log_id);

        if( ! empty($transfer_log) && $transfer_log->canUndo) {

//                $can_reverse = false;
//                $transfer_log->transfer_log_details->each(function($transfer_log_detail) use ($can_reverse) {
//                    $batch_created = $transfer_log_detail->batch_created;
//                    if($transfer_log_detail->units <= $batch_created->inventory) {
//                        $can_reverse = true;
//                    }
//                });
//
//                if( ! $can_reverse) {
//                    $this->error($transfer_log->id.' Unable to reverse!');
//                    return;
//                }

            try {

                DB::beginTransaction();

                $transfer_log->transfer_log_details->each(function ($transfer_log_detail) {

                    $transfer_log_detail_id = $transfer_log_detail->id;
                    $batch_created = $transfer_log_detail->batch_created;

//                dump($transfer_log_detail->units);
//                dump($batch_created->inventory);
//dd($transfer_log_detail->units <= $batch_created->inventory);

                    if ($transfer_log_detail->units <= $batch_created->inventory) {
                        $batch_created->inventory -= $transfer_log_detail->units;

                        $batch_created->save();

                        $batch_id = $batch_created->id;

                        if ($transfer_log_detail->action == 'Created' &&
                            $batch_created->inventory == 0 &&
                            (!$batch_created->order_details->count())) {

                            $transfer_log_detail->delete();
                            $this->info('transfer_log_detail id: ' . $transfer_log_detail_id . ' Deleted');

                            $batch_created->delete();
                            $this->info('batch id: ' . $batch_id . ' Deleted');

                        } else {

                            $this->error('batch id: ' . $batch_id . ' Not deleted!');
                        }

                    } else {

                        $this->error('transfer_log_detail id: ' . $transfer_log_detail->id . ' Unable to reverse! - Not enough units available.');
                        return;
                    }

                });

                $transfer_log->batch_converted->refresh();

                $transfer_log->batch_converted->inventory += $transfer_log->quantity_transferred;
//                $transfer_log->batch_converted->transfer -= $transfer_log->quantity_transferred;

                $transfer_log->batch_converted->save();

                $transfer_log->delete();

                DB::commit();

                $this->info('Transfer id:' . $transfer_log_id . " reversed and deleted !");

            }
            catch(\Exception $e) {
                DB::rollBack();

                throw new \Exception('Unable to undo!');

            }
        }



    }
}
