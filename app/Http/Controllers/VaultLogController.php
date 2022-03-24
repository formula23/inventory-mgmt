<?php

namespace App\Http\Controllers;

use App\Broker;
use App\Filters\VaultLogFilters;
use App\SaleOrder;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Twilio;
use App\Batch;
use App\User;
use App\VaultLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Ultraware\Roles\Models\Role;

class VaultLogController extends Controller
{


    public function __construct()
    {
        //http://highline.test/vault-logs/login/1A406030000592E000009832
        $this->middleware('auth', ['except' => ['login','forceLogin']]);

        view()->share('warnings', collect());
        view()->share('title', $this->construct_title()?:'Dashboard');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(VaultLogFilters $vaultLogFilters)
    {
//        dd($vaultLogFilters);
        $vault_logs_qry = VaultLog::filters($vaultLogFilters)->orderBy('created_at', 'desc')->with(['user','batch.purchase_order.vendor', 'order_detail.sale_order', 'broker']);
        if(request()->has('vault_log_session')) {
            $vault_logs_qry->where('session_id', request()->get('vault_log_session'));
        }
//        dd($vault_logs_qry);
        $vault_logs = $vault_logs_qry->paginate(100);
//dd($vault_logs);
        $brokers = Broker::orderBy('name')->pluck('name','id');

        $filters = $vaultLogFilters->getFilters()->toArray();

        $open_sales_orders = SaleOrder::openOrders()->with('destination_license', 'customer', 'broker')->get();

        return view('vault_logs.index', compact('vault_logs', 'open_sales_orders', 'filters', 'brokers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //get current items saved

        $vault_logs = VaultLog::currentSessionLogs()->get();

        $broker = Broker::find(session('broker_id'));
        $brokers = Broker::orderBy('name')->pluck('name','id');

        $batch = Batch::whereRefNumber(request('ref_number'))->with('vault_logs','order_details_cog')->first();


        return view('vault_logs.create', compact('batch', 'vault_logs', 'brokers', 'broker'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        dd($request->all());

        try {

            DB::beginTransaction();

            //create new or add
            $vaultlog = VaultLog::where('session_id', session()->getId())
                ->where('batch_id', $request->get('batch_id'))
                ->where('strain_name', $request->get('strain_name'))
                ->where('price', $request->get('price')*100)
                ->first();

            $qty = ($request->get('quantity') * ( ($request->get('in_out')=='out')? -1 : 1 ));

            if(!is_null($vaultlog)) {

                $vaultlog->quantity += $qty;
                $vaultlog->save();

            } else {

                $broker_id = $request->get('broker_id');

                if( ! is_null($request->get('new_broker')) ) { //create new broker user
                    $new_broker = Broker::create_new_broker($request->get('new_broker'));
                    $broker_id = $new_broker->id;
                }

                $vaultlog_data = [
                    'user_id'=>Auth::user()->id,
                    'batch_id'=>$request->get('batch_id'),
                    'broker_id'=>$broker_id,
                    'session_id'=>session()->getId(),
                    'order_title'=>"",
                    'strain_name'=>$request->get('strain_name'),
                    'notes'=>$request->get('notes'),
                    'price'=>$request->get('price'),
                    'quantity'=>$qty,
                ];

                //save order title in sessions
                session([
                    'broker_id' => $broker_id,
                    'in_out' => $request->get('in_out'),
                ]);

                VaultLog::create($vaultlog_data);

            }

            DB::commit();

            return redirect(route('vault-logs.create'));

        } catch(\Exception $e) {

            DB::rollBack();

            flash()->error('Error: '.$e->getMessage());

            return back();
        }


    }

    public function addToSaleOrder(Request $request, VaultLog $vaultLog)
    {
//        dump($request->all());
//        dump($vaultLog);

        if(! $request->get('sale_order_id') || $vaultLog->quantity > 0) {
            flash()->error('Error');
            return redirect(route('vault-logs.index'));
        }

        $sale_order = SaleOrder::find($request->get('sale_order_id'));

        try {

            DB::beginTransaction();

            $sale_price = $vaultLog->batch->unit_price + $request->get('cost_markup');

            $sale_order->addUpdateItem($vaultLog->batch, $request->get('sold_as_name'), ($vaultLog->quantity*-1), $sale_price);

            $vaultLog->order_detail_id = $sale_order->latest_order_detail->id;
            $vaultLog->save();

            $sale_order->calculateTotals();

            DB::commit();

            flash()->success($vaultLog->batch->name.' added to sale order');

            return redirect()->back();

        } catch(QueryException $e) {
            DB::rollBack();
            if($e->getCode() == 22003) {
                flash()->error('Unable to add item to order.');
            } else {
                flash()->error($e->getMessage());
            }

            return redirect()->back();
        }

//        dump($sale_price);
//        dd($sale_order);
    }

    public function complete(Request $request)
    {

        $this->send_vault_log_sms();

        //logout redirect
        Auth::guard()->logout();
        $request->session()->invalidate();
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VaultLog  $vaultLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(VaultLog $vaultLog)
    {
        $vaultLog->delete();
        return redirect(route('vault-logs.create'));
    }

    public function returnOrder(Request $request, $vault_log_session)
    {

        $random_str = Str::random(30);

        $vault_logs = VaultLog::where('session_id', $vault_log_session)->get();

        $order_title = $vault_logs->first()->order_title." (RETURN)";

        foreach($vault_logs as $vault_log)
        {
            $vaultlog_data = [
                'user_id'=>Auth::user()->id,
                'batch_id'=>$vault_log->batch_id,
                'broker_id'=>$vault_log->broker_id,
                'session_id'=>$random_str,
                'order_title'=>$order_title,
                'strain_name'=>$vault_log->strain_name,
                'notes'=>null,
                'price'=>null,
                'quantity'=>$vault_log->quantity * -1,
            ];

            VaultLog::create($vaultlog_data);
        }

        $this->send_vault_log_sms($random_str);

        return redirect(route('vault-logs.index'));

    }


    public function login()
    {
        view()->share('title', 'Vault Check-out');

        $ref_number = request('ref_number');

        //98.152.223.68 HL
        // Dan
//        dump(App::environment('production'));
//        dump(request()->ip());
//        dd(Batch::whereRefNumber($ref_number)->exists());
//
        if(App::environment('production') && !in_array(request()->ip(), ["98.152.223.67", "67.49.97.167", "127.0.0.1"])
            || Batch::whereRefNumber($ref_number)->exists()==false) { //check IP address
            return redirect('login');
        }

        if(Auth::check()) {
            return redirect(route('vault-logs.create', $ref_number));
        }

        $users = User::whereIn('id', config('highline.vault_log_access'))->pluck('name','id');

        return view('vault_logs.login', compact('users'));
    }

    public function forceLogin()
    {
        $user = User::find(request('user_id'));

        if($user && Hash::check(request()->get('pin'), $user->pin)) {
            Auth::login($user);
            return redirect(route('vault-logs.create', request('ref_number')));
        }

        flash('Access Denied!')->error();
        return back();
    }

    protected function send_vault_log_sms($local_session=null)
    {
        //construct message
        $local_session = ($local_session ? $local_session : session()->getId());

        $vault_logs = VaultLog::currentSessionLogs($local_session)->get();

        $log_items=collect();
        $vault_logs->each(function($vault_log) use ($log_items) {

            $log_items->push(trans('messages.vault_log_notification.sms_complete_item', [
                'qty'=>abs($vault_log->quantity),
                'strain'=>$vault_log->present()->strain_notes_price(
                    PHP_EOL,
                    ($vault_log->price ? display_currency($vault_log->price, 0,1,""): "")
                ),
            ]));
        });

        $message = trans('messages.vault_log_notification.sms_complete', [
            'order_date'=>$vault_logs->first()->created_at->format('m/d/Y'),
            'broker'=>$vault_logs->first()->broker->name.(session('in_out')=='in'?PHP_EOL.'RETURN':''),
            'items'=>$log_items->implode(PHP_EOL)
        ]);

        if(config('app.env')=='production') {
            //get all recipients
            $users = User::select('phone')->whereIn('id', config('highline.vault_log_sms_ids'))->get();
            $users->each(function($user) use ($message) {
                Twilio::message($user->getOriginal('phone'), $message);
            });
        } else {
            Twilio::message("+13106004938", $message);
        }
    }

    public function resetFilters(VaultLogFilters $vaultLogFilters)
    {
        $vaultLogFilters->resetFilters();
        return redirect(route('vault-logs.index'));
    }
}
