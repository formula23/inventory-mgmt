<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Order;
use App\OrderTransaction;
use App\SaleOrder;
use App\SalesCommission;
use App\SalesCommissionDetail;
use App\TransferLog;
use App\User;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AccountingController extends Controller
{

    public function transactions()
    {

        view()->share('title', 'Accounting Transactions');

        $order_transactions = OrderTransaction::with(['purchase_order.vendor','sale_order.customer'])->orderBy('id', 'desc')->paginate(50)->withPath(route('accounting.transactions'));

//            dd($order_transactions->first());

        return view('accounting.transactions', compact('order_transactions'));

    }

    public function payables()
    {
        if(Gate::denies('accounting.receivables.index')) {
            return redirect(route('dashboard'));
        }

        view()->share('title', 'Accounting Payables');

        $vendors = Vendor::withBalance()->get();

        return view('accounting.payables', compact('vendors'));
    }

    public function receivables()
    {
        if(Gate::denies('accounting.receivables.index')) {
            return redirect(route('dashboard'));
        }

        view()->share('title', 'Accounting Receivables');

        $all_customers = Customer::withBalance()->get();
        $bulk_customers = Customer::withBalance('where', 'bulk')->where('orders.sale_type','bulk')->get();
        $packaged_customers = Customer::withBalance('where', 'packaged')->where('orders.sale_type','packaged')->get();
        $other_customers = Customer::withBalance('whereNotIn', ['bulk', 'packaged'])->whereNotIn('orders.sale_type', ['bulk', 'packaged'])->get();

        return view('accounting.receivables', compact('all_customers', 'bulk_customers', 'packaged_customers', 'other_customers'));
    }

    public function receivables_aging()
    {
        if(Gate::denies('accounting.receivables.aging')) {
            return redirect(route('dashboard'));
        }

        view()->share('title', 'Accounting Receivables > Aging');

        $sale_orders = SaleOrder::withOutstandingBalance()->with('customer')->get();

        return view('accounting.receivables_aging', compact('sale_orders'));
    }

    public function inventory_loss()
    {
        view()->share('title','Accounting / Inventory Loss');

        $inventory_loss = TransferLog::inventoryLoss()->get();
        return view('accounting.inventory-loss', compact('inventory_loss'));
    }

    public function sales_rep_commissions()
    {
        view()->share('title','Accounting / Sales Rep Commissions');

        $sales_rep=null;
        $sale_orders=null;
        $start_date=null;
        $end_date=null;
        $commission_rate=0;
        $sales_commissions=null;

        $sales_reps = User::salesReps()->where('active',1)->orderBy('name')->get()->pluck('name', 'id');

        $sales_commission=null;
        if($sales_commission_id = request('sales_commission_id')) {
            $sales_commission = SalesCommission::where('id',$sales_commission_id)->with(['user','sales_rep', 'sales_commission_details.sale_order.customer'])->first();
        }

        if($sales_rep_id = request('sales_rep_id')) {

            $sales_rep = User::find($sales_rep_id);

            $commission_rate = ($sales_rep->hasRole('salesmanager') ? 0.01 : 0.07);

            if(request('end_date')=='2018-09-22') {
                $end_date = Carbon::createFromFormat('Y-m-d', request('end_date'));
                $start_date = Carbon::createFromFormat('Y-m-d', '2018-09-01');
            } else {
                $end_date = Carbon::createFromFormat('Y-m-d', request('end_date'));
                $start_date = $end_date->copy()->subWeek(1)->startOfWeek();
            }

            ///get existing sales commissions
            $sales_commissions = $sales_rep->my_sales_commissions;

            $sales_commissions->transform(function($sales_commission) {
                $sales_commission['pay_period'] = $sales_commission->period_start_formatted." - ".$sales_commission->period_end_formatted;
                return $sales_commission;
            });

//            dd($sales_commissions);

            $sale_orders=null;
            if( ! $sales_rep->hasSalesCommForPeriod($start_date, $end_date) )
            {
                //get orders
                $sale_orders_qry = SaleOrder::select('orders.*')
                    ->with(['customer','sales_rep'])
                    ->with(['sales_commission_details'=>function($qry) use ($sales_rep) {
                        $qry->where('sales_rep_id', $sales_rep->id);
                    }])
                    ->whereDoesntHave('transactions', function($qry) use($end_date) {
                        $qry->whereDate('txn_date','>',$end_date->toDateString());
                    })
//                ->whereDoesntHave('sales_commissions')
                    ->with('customer.first_sale_order')
                    ->with('customer.sale_orders')
                    ->whereDate('orders.txn_date','>',config('highline.sales_commission_start_date'))
                    ->whereDate('orders.txn_date','<=',$end_date->toDateString())
                    ->where(function($qry) {
                        $qry->where('balance','<=',0)->orWhereIn('customer_id', ['95','34']);
                    })
                    ->whereIn('sale_type', ['packaged','bulk'])
                    ->whereIn('status', ['delivered','returned'])
                    ->orderBy('orders.txn_date', 'desc');

                if( ! $sales_rep->hasRole('salesmanager')) {
                    $sale_orders_qry->where('sales_rep_id', $sales_rep->id);
                } else {
                    $sale_orders_qry->whereNotNull('sales_rep_id');
                }

                $sale_orders = $sale_orders_qry->get();
            }
//dd($sale_orders);
        }

        return view('accounting.sales_rep_commissions', compact('sales_reps', 'sales_rep', 'sale_orders', 'start_date', 'end_date', 'commission_rate', 'sales_commissions', 'sales_commission'));
    }

    public function sales_rep_commissions_store(Request $request)
    {

        $sale_orders = collect($request->get('sale_orders'))->transform(function($t) use ($request) {
            $t['rate']/=100;
            $t['amount'] = round($t['subtotal'] * $t['rate'], 2);
            $t['sales_rep_id'] = $request->get('sales_rep_id');
            return new SalesCommissionDetail($t);
        });

        $sales_commission = Auth::user()->created_sales_commissions()->save(new SalesCommission([
            'sales_rep_id'=>$request->get('sales_rep_id'),
            'period_start'=>$request->get('period_start'),
            'period_end'=>$request->get('period_end'),
            'total_revenue'=>$request->get('total_revenue'),
            'total_commission'=>$sale_orders->sum('amount'),
        ]));

        $sales_commission->sales_commission_details()->saveMany($sale_orders);

        flash()->success('Sales Commissions Saved!');

        return back()->withInput();

    }


}
