<?php

namespace App\Http\Controllers;

use App\Broker;
use App\Filters\SaleOrderFilters;
use App\Order;
use App\OrderDetail;
use App\Repositories\Contracts\SaleOrderRepositoryInterface;
use App\SaleOrder;
use App\TransferLog;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\DbUserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleOrdersController extends Controller
{

    public function __construct(SaleOrderRepositoryInterface $saleOrderRepositoryInterface)
    {
        parent::__construct();

        $this->sale_order = $saleOrderRepositoryInterface;
    }

    public function index(Request $request, SaleOrderFilters $saleOrderFilters)
    {
        $sale_orders = SaleOrder::filters($saleOrderFilters)->get()->sortByDesc('txn_date');
        $sale_orders->load(['customer','bill_to','sales_rep','user','sales_commission_details', 'order_details_cog.sale_order', 'order_details_cog.batch.fund','destination_license']);

//dd($sale_orders->groupBy('status'));
//        dump($sale_orders->get(0));
//dd($sale_orders->first()->margin);
        $filters = $saleOrderFilters->getFilters()->toArray();

        $filter_customer=null;
        if(isset($filters['customer'])) {
            $filter_customer = User::find($filters['customer']);
        }

        $customer_types = SaleOrder::customer_type()->pluck('customer_type');

        $customers = (new DbUserRepository)->customers(null)->get();
        $sales_reps = (new DbUserRepository)->sales_reps()->get();
        $brokers = Broker::orderBy('name')->pluck('name','id');

        return view('sale_orders.index', compact('sale_orders','filters', 'customers', 'sales_reps', 'customer_types', 'brokers', 'filter_customer'));
    }

    public function show(SaleOrder $saleOrder)
    {

        $saleOrder->load([
            'sales_rep',
            'broker',
            'order_details.sale_order',
            'order_details.batch.category',
            'order_details.batch.brand',
            'order_details.batch.fund',
            'order_details.batch.tax_rate',
            'order_details.order_detail_returned',
            'order_details.batch.parent_batch',
            'order_details.batch.parent_batch.parent_batch',
            'order_details.batch.parent_batch.parent_batch.parent_batch',
            'order_details_cog.batch.fund',
            'sales_commission_details.sales_rep',
        ]);

        $sales_reps = (new DbUserRepository)->sales_reps()->pluck('name','id');
        $brokers = Broker::orderBy('name')->pluck('name','id');

        return view('sale_orders.show', compact('saleOrder', 'sales_reps', 'brokers'));
    }

    public function retagUids(Request $request, SaleOrder $saleOrder)
    {
        $saleOrder->load([
            'order_details.sale_order',
            'order_details.batch.category',
            'destination_license.license_type',
        ]);

        return view('sale_orders.retag_uids', compact('saleOrder'));
    }

    public function retagUidsProcess(Request $request)
    {
        try {

            foreach($request->get('new_uids') as $order_detail_id => $new_uid) {

                if(is_null($new_uid)) continue;
                //retag batch

                $orderDetail = OrderDetail::with('batch')->find($order_detail_id);

                $qty_to_xfer = $orderDetail->units;
                $used_weight = ($orderDetail->batch->uom == 'g') ? $qty_to_xfer : $qty_to_xfer * config('highline.uom.lb');

                //amount
                $amount = $orderDetail->units;
                $uom = $orderDetail->batch->uom;

                $packages_created = [
                    [
                        "ref_number"=>$new_uid,
                        "category_id" => $orderDetail->batch->category_id,
                        "brand_id" => null,
                        "amount" => $amount,
                        "uom" => $uom,
                        "packed_date" => Carbon::today(),
                        "fund_id" => $orderDetail->batch->fund_id,
                    ]
                ];

                $new_batch = $orderDetail->batch->transfer(
                    $used_weight,
                    $qty_to_xfer,
                    $packages_created
                );

                $new_batch->inventory = 0;
                $new_batch->save();

                $orderDetail->batch_id = $new_batch->id;
                $orderDetail->save();

            }

            flash()->success('Batches retagged!');

        } catch(\Exception $e) {
            DB::rollBack();
//            dd($e);
//            Bugsnag::notifyException($e);
            flash()->error('Error: '.$e->getMessage());
        }

        return redirect()->back();
    }

    public function uidExport(Request $request, SaleOrder $saleOrder)
    {

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=".$saleOrder->ref_number."-UIDs-Metrc.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function() use ($saleOrder)
        {
            $file = fopen('php://output', 'w');

            foreach($saleOrder->order_details as $order_detail)
            {
                if(!empty($order_detail->batch)) {
                    fputcsv($file, [$order_detail->batch->ref_number]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function update(Request $request, SaleOrder $saleOrder)
    {
        $saleOrder->status = $request->has('status') ? $request->get('status') : $saleOrder->status ;
        $saleOrder->inv_number = ($request->has('inv_number') ? $request->get('inv_number') : $saleOrder->inv_number );
        $saleOrder->manifest_no = ($request->has('manifest_no') ? $request->get('manifest_no') : $saleOrder->manifest_no );
        $saleOrder->terms = ($request->has('terms') ? $request->get('terms') : $saleOrder->terms);
        $saleOrder->txn_date = ($request->has('txn_date') ? $request->get('txn_date'): $saleOrder->txn_date);

        $saleOrder->sales_rep_id = ($request->has('sales_rep_id') ? $request->get('sales_rep_id'): $saleOrder->sales_rep_id);
        $saleOrder->broker_id = ($request->has('broker_id') ? $request->get('broker_id'): $saleOrder->broker_id);

        $saleOrder->expected_delivery_date = ($request->has('expected_delivery_date') ? $request->get('expected_delivery_date') : $saleOrder->expected_delivery_date);

        $saleOrder->setDueDate();

//        $saleOrder->due_date = $saleOrder->txn_date->addDays($saleOrder->terms);

        $saleOrder->notes = $request->has('notes') ? $request->get('notes') : $saleOrder->notes;
        $saleOrder->order_notes = $request->has('order_notes') ? $request->get('order_notes') : $saleOrder->order_notes;
        $saleOrder->in_qb = $request->has('in_qb') ? $request->get('in_qb'): $saleOrder->in_qb;
        $saleOrder->save();

        if( request()->wantsJson() )
        {
            return response()->json($saleOrder);
        }
        flash()->success('Sales Order Updated!');

        return redirect(route('sale-orders.show', $saleOrder->id));
    }

    public function applyDiscount(Request $request, SaleOrder $saleOrder)
    {
//        dd($saleOrder);
        switch($request->get('discount_type'))
        {
            case 'perc':
                if($request->get('discount_applied') <= 0 || $request->get('discount_applied') > 100)
                {
                    $request->flash();
                    flash()->error('Discount percentage out of range.');
                    return redirect(route('sale-orders.show', $saleOrder->id));
                }
                break;
            case 'amt':
                if($request->get('discount_applied') <= 0)
                {
                    $request->flash();
                    flash()->error('Invalid discount amount.');
                    return redirect(route('sale-orders.show', $saleOrder->id));
                }
                break;
            default:
                if($request->get('discount_applied'))
                {
                    $request->flash();
                    flash()->error('Please select a discount type if applying a discount.');
                    return redirect(route('sale-orders.show', $saleOrder->id));
                }

        }
//dump($request->get('discount_applied', 0));
        $saleOrder->discount_description = ($request->get('discount_type') == 'none' ? '' : $request->get('discount_description'));
        $saleOrder->discount_applied = $request->get('discount_applied', 0);
        $saleOrder->discount_type = $request->get('discount_type');
//dd($saleOrder);
//        dump($saleOrder->discount_applied);
//        dd('te');
//        $saleOrder->save();

//        dump('disc applied');
//        dump($saleOrder->discount_applied);
        if($saleOrder->discount_applied)
        {
            if($saleOrder->discount_type == 'perc')
            {
                $saleOrder->discount = $saleOrder->subtotal * ($saleOrder->discount_applied / 100);
            } else {
                $saleOrder->discount = $saleOrder->discount_applied;
            }

        } else {
            $saleOrder->discount=0;
        }
        $saleOrder->save();
        $saleOrder->calculateTotals();

        flash()->success('Discount applied');

        return redirect(route('sale-orders.show', $saleOrder->id));
    }

    public function invoice(SaleOrder $saleOrder)
    {

        $saleOrder->load('order_details.batch.category','bill_to', 'order_details.batch.brand', 'order_details.sale_order');

        $pdf = PDF::loadView('sale_orders.invoice', compact('saleOrder'));

//        return view('sale_orders.invoice', compact('saleOrder'));

        return $pdf->download(\Str::slug($saleOrder->customer->name).'-'.$saleOrder->ref_number.'.pdf');
    }

    public function shippingManifest(SaleOrder $saleOrder)
    {
        $saleOrder->load('order_details.batch.category');

        return view('sale_orders.shipping-manifest', compact('saleOrder'));
    }

    public function remove(SaleOrder $saleOrder)
    {
        $saleOrder->delete();

        return redirect(route('sale-orders.index', $saleOrder->id));

    }

    public function removeItem(SaleOrder $saleOrder, OrderDetail $orderDetail)
    {
        if($saleOrder->id != $orderDetail->sale_order_id) {
            flash()->error('Product sale order doesn\'t match');
            return redirect(route('sale-orders.show', $saleOrder->id));
        }

        $removeitem = $saleOrder->removeItem($orderDetail);

        if($removeitem instanceof OrderDetail) {
            $saleOrder->calculateTotals();
            return redirect(route('sale-orders.show', $saleOrder->id));
        } else {
            flash()->error($removeitem->getMessage());
            return redirect(route('sale-orders.show', $saleOrder->id));
        }
    }

    public function acceptOrderDetail(SaleOrder $saleOrder, OrderDetail $orderDetail)
    {
        $units_rejected = $orderDetail->units - request('units_accepted');

        $orderDetail->batch->inventory += $units_rejected;
        $orderDetail->batch->save();

//        $orderDetail->subtotal_sale_price = request('units_accepted') * $orderDetail->unit_sale_price;
        $orderDetail->units_accepted = request('units_accepted');
        $orderDetail->save();

        if($saleOrder->order_details()->whereNull('units_accepted')->count() == 0)
        {
            $saleOrder->status = 'delivered';
            $saleOrder->save();
        }

        $saleOrder->calculateTotals();

        return back();
    }

    public function acceptAll(SaleOrder $saleOrder)
    {
        $saleOrder->order_details->map(function($order_detail) {
            if(empty($order_detail->batch)) return;
            if( ! is_null($order_detail->units_accepted)) return;
            $order_detail->units_accepted = $order_detail->units;
            $order_detail->save();
        });
        $saleOrder->delivered();
        return back();
    }

    public function close(SaleOrder $saleOrder)
    {
        $saleOrder->close();
        return back();
    }

    public function open(SaleOrder $saleOrder)
    {
        $saleOrder->open();
        return back();
    }

    public function readyForDelivery(SaleOrder $saleOrder)
    {
        $saleOrder->ready_for_delivery();
        return back();
    }

    public function inTransit(SaleOrder $saleOrder)
    {
        $saleOrder->in_transit();
        return back();
    }

    public function payment(SaleOrder $saleOrder)
    {
        $payment = request('payment');
        $txn_date = request('txn_date');
        $payment_method = request('payment_method');
        $ref_number = request('ref_number');
        $memo = request('memo');

//        if($payment > $saleOrder->balance) {
//            flash()->error('Cannot save a payment amount great than balance');
//            return redirect(route('sale-orders.show', $saleOrder->id));
//        }

        $saleOrder->applyPayment($payment, $txn_date, $payment_method, $ref_number, $memo);

        flash()->success('Payment applied');
        return redirect(route('sale-orders.show', $saleOrder->id));

    }

    public function resetFilters(SaleOrderFilters $saleOrderFilters)
    {
        $saleOrderFilters->resetFilters();
        return redirect(route('sale-orders.index'));
    }

    public function vaultLog()
    {


        return view('sale_orders.vault-log');
    }

}
