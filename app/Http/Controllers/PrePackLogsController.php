<?php

namespace App\Http\Controllers;

use App\TransferLog;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class PrePackLogsController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        view()->share('title','Pre-pack Logs');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Gate::denies('prepacklogs.show')) {
            flash()->error('Access Denied');
            return back();
        }

        $transfer_logs = TransferLog::where('type', 'Pre-Pack')
            ->with('user','batch_converted','transfer_log_details.batch_created.category')
            ->orderBy('created_at','desc')
            ->paginate(10);

        return view('prepack_logs.index', compact('transfer_logs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TransferLog  $transferLog
     * @return \Illuminate\Http\Response
     */
    public function show(TransferLog $transferLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TransferLog  $transferLog
     * @return \Illuminate\Http\Response
     */
    public function edit(TransferLog $transferLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TransferLog  $transferLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransferLog $transferLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TransferLog  $transferLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransferLog $transferLog)
    {
        //
    }
}
