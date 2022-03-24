<?php

namespace App\Http\Controllers;


use App\Batch;
use App\Customer;
use App\PurchaseOrder;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\SaleOrder;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Milon\Barcode\DNS2D;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

//        $created_batch = Batch::find(12171);
//
//        dump($created_batch);
//
//        dump($created_batch->name);
//        dump($created_batch->units_purchased);
//        dump($created_batch->inventory);
//        dump($created_batch->uom);
//
//        dump('source batches');
//        foreach($created_batch->transfer_log_details  as $transfer_log_detail) {
//            dump($transfer_log_detail->transfer_log->quantity_transferred);
//            dump($transfer_log_detail->transfer_log->batch_converted->uom);
//        }
//
////        dump($created_batch->transfer_log_details);
//
//        dd('d');

        $todays_orders = (new SaleOrder())->todaysOrders();

        $weeks_orders = (new SaleOrder())->weeksOrders();

        $months_orders = (new SaleOrder())->monthsOrders();

        $quarter_orders = (new SaleOrder())->quartersOrders();

        $excise_tax = (new SaleOrder())->exciseTax();

        $customers = (new User())->all_customers_ordered_last();

//        $customers = User::customers()
//            ->select('users.id', 'users.name',
//                DB::raw('min(orders.txn_date) as first_order'),
//                DB::raw('max(orders.txn_date) as last_order'),
//                DB::raw('count(orders.id) as number_of_orders'),
//                DB::raw('sum(orders.subtotal/100) as total_order_value'),
//                DB::raw('datediff(now(), max(orders.txn_date)) AS `days_last_order`')
//            )
//            ->where('active', 1)
//            ->join('orders','users.id','=','orders.customer_id')
//            ->groupBy('users.id')
//            ->orderBy('days_last_order', 'desc')
//            ->get();



        //past 3 months

        $new_customers = User::customers()
            ->select('users.id', 'users.name',
                DB::raw('min(orders.txn_date) as first_order'),
                DB::raw('min(sales_rep.name) as sales_rep_name'),
                DB::raw('date_format(min(users.created_at), "%M") as first_order_month'),
                DB::raw('count(orders.id) as number_of_orders'),
                DB::raw('sum(orders.subtotal/100) as total_order_value')
//                        DB::raw('month(orders.txn_date) as added_month')
            )
            ->join('orders','users.id','=','orders.customer_id')
            ->leftJoin('users as sales_rep', 'sales_rep.id', '=', 'orders.sales_rep_id')
            ->where('users.active', 1)
            ->whereDate('users.created_at','>=',Carbon::now()->subMonth(2)->firstOfMonth())
            ->groupBy('users.id')
            ->orderBy('first_order', 'desc')
            ->get()->groupBy('first_order_month');


        $url = URL::temporarySignedRoute(
            'login',
            now()->addMinutes(30),
            ['user' => Auth::user()->id]
        );
//dd($url);
        return view('index', compact(
            'todays_orders',
            'weeks_orders',
            'months_orders',
            'quarter_orders',
            'customers',
            'new_customers',
            'excise_tax'
        ));
    }

    public function search()
    {
        if( ! $q = request('q')) return redirect(route('dashboard'));

        $purchase_orders = PurchaseOrder::search($q)->get();
        $sale_orders = SaleOrder::search($q)->get();
        $vendors = Vendor::search($q)->get();
        $customers = Customer::search($q)->get();
        $batches = Batch::search($q)->get();

        return view('search', compact('purchase_orders', 'sale_orders', 'vendors', 'customers', 'batches'));

    }

}
