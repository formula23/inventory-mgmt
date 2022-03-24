<?php

namespace App\Console\Commands;

use App\TransferLog;
use Illuminate\Console\Command;

class FixReverseReconcile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:reverse_reconcile';

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
        if(\App::environment('production')) {
            $this->error('Disabled in Production!');
            return;
        }

        $transfer_id = $this->ask('Transfer Log Id?');

        $transfer_log_builder = TransferLog::with('batch_converted')
            ->where('Type','Reconcile');

        if($transfer_id) {
            $transfer_log = $transfer_log_builder->findOrFail($transfer_id);
//            dd($transfer_log);
            if($transfer_log->undo()) {
                $this->info($transfer_id." - Undone & Deleted!");
            } else {
                $this->error($transfer_id." - Unable to reverse/delete. Please review!");
            }
        } else {
            $trans_logs = $transfer_log_builder
                ->whereDate('created_at','>=','2019-04-02')
                ->get();

            $trans_logs->each(function($tran_log) {
                $transfer_id = $tran_log->id;
                if($tran_log->undo()) {
                    $this->info($transfer_id." - Undone & Deleted!");
                } else {
                    $this->error($transfer_id." - Unable to reverse/delete. Please review!");
                }
            });
        }


    }
}
