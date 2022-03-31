<?php

namespace App\Http\Controllers;

use App\Batch;
use App\Brand;
use App\Category;
use App\Conversion;
use App\Filters\PurchaseOrderFilters;
use App\Fund;
use App\License;
use App\TaxRate;
use App\TransferLog;
use App\Vendor;
use App\Order;
use App\PurchaseOrder;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\DbOrderRepository;
use App\Repositories\DbPurchaseOrderRepository;
use App\Repositories\DbUserRepository;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class PurchaseOrdersController extends Controller
{
    protected $purchase_order;

    public function __construct(PurchaseOrderRepositoryInterface $purchaseOrderRepositoryInterface)
    {
        parent::__construct();

        $this->purchase_order = $purchaseOrderRepositoryInterface;

    }

    public function index(PurchaseOrderFilters $purchaseOrderFilters)
    {
        if(Gate::denies('po.index')) {
            flash()->error('Access Denied');
            return back();
        }

        $purchase_orders = PurchaseOrder::with(['batches','fund'])->filters($purchaseOrderFilters)
            ->get()->sortByDesc('txn_date');

        $filters = $purchaseOrderFilters->getFilters()->toArray();
        $funds = Fund::pluck('name', 'id');

        $vendors = (new DbUserRepository)->vendors()->get();

        return view('purchase_orders.index', compact('purchase_orders','vendors','filters','funds'));
    }

    public function create(Request $request, User $vendor)
    {
        if(Gate::denies('po.create')) {
            flash()->error('Access Denied');
            return back();
        }

        $segment_name = $request->segment(2);

        $vendors = Vendor::orderBy('name')->pluck('name', 'id');
        $funds = Fund::pluck('name', 'id');

        $tax_rates = TaxRate::cultivation_tax_rates();

        //system licenses
        $destination_licenses = License::system_licenses()->get();

        $categories = Category::active()->get();
        $brands = Brand::orderBy('name')->get();

        $vendor->load('licenses');

//dd($destination_licenses);
        return view('purchase_orders.create', compact(
            'vendors',
            'vendor',
            'categories',
            'brands',
            'funds',
            'destination_licenses',
            'segment_name',
            'tax_rates'));
    }

    public function show(Request $request, PurchaseOrder $purchaseOrder)
    {
        if(Gate::denies('po.show')) {
            flash()->error('Access Denied');
            return back();
        }

        $conv = Conversion::getRates();

        $tax_rates = TaxRate::cultivation_tax_rates();
        $categories = Category::active()->get();
        $brands = Brand::orderBy('name')->get();

        if($request->isMethod('post'))
        {
            try {

                $batch = null;
                foreach(request()->get('_batches') as $field=>$vals)
                {
                    $batch[$field] = $vals[0];
                }

                DB::beginTransaction();

                $added_batch = $purchaseOrder->addBatch($batch);

                $purchaseOrder->refresh();
                $purchaseOrder->updateTotals();

                DB::commit();

                flash()->success('Batch added!');

            } catch(\Exception $e) {
                DB::rollBack();
                flash()->error($e->getMessage());
                return redirect(route('purchase-orders.show', $purchaseOrder->id))
                    ->withInput($request->all());
            }
        }

        $purchaseOrder->load([
            'vendor',
            'originating_entity',
            'origin_license',
            'batches.children_batches.order_details',
            'batches.brand',
            'batches.category',
            'batches.cultivator',
            'batches.fund',
            'batches.order_details',
            'fund',
            'destination_license.license_type',
            'transactions' => function($q) {
                $q->orderBy('txn_date');
            }
        ]);



//dd($purchaseOrder);
        return view('purchase_orders.show', compact('purchaseOrder', 'categories', 'brands', 'tax_rates') );
    }

    public function store(Request $request)
    {

        $data = $request->all();

        try {
            DB::beginTransaction();
//dd($data);
            $po = $this->purchase_order->create($data);

            DB::commit();
            flash()->success('Purchase Order #'.$po->ref_number.' Created');
            return redirect(route('purchase-orders.show', $po->id));

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            flash()->error($e->getMessage());

            return back()->withInput($request->all());
        }

    }

    public function remove(PurchaseOrder $purchaseOrder)
    {
        try {

            DB::beginTransaction();

            if($purchaseOrder->batches->count()) {
                $purchaseOrder->batches->each(function($batch) {
                    if(bccomp($batch->units_purchased, $batch->inventory, 4) !== 0) {
                        throw new \Exception("Error - unable to delete: ".$batch->ref_number);
                    }
                    $batch->delete();
                });
            }

            $purchaseOrder->delete();

            DB::commit();

            return redirect(route('purchase-orders.index'));

        } catch(\Exception $e) {

            DB::rollBack();
            flash()->error($e->getMessage());
            return redirect(route('purchase-orders.show', $purchaseOrder));

        }

    }

    public function printPo(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'transactions' => function($q) {
                $q->orderBy('txn_date');
            }
        ]);

        $pdf = PDF::loadView('purchase_orders.print_po', compact('purchaseOrder'));

//        return view('purchase_orders.print_po', compact('purchaseOrder'));

        return $pdf->download(\Str::slug($purchaseOrder->vendor->name).'-'.$purchaseOrder->ref_number.'.pdf');

    }

    public function printQr(PurchaseOrder $purchaseOrder)
    {
        if(Gate::denies('po.printqr')) {
            flash()->error('Access Denied');
            return back();
        }

        $qr_code_collection = new Collection();

        foreach($purchaseOrder->batches as $batch) {

            $gofor=($batch->uom == 'lb' ? round($batch->inventory) : 6);
            for($i=1; $i<=$gofor; $i++) {
                $qr_code_collection->push($batch);
            }
        }

        $purchaseOrder->load('batches');

//        return view('purchase_orders.print-qr-lg', compact('purchaseOrder'));
        return view('purchase_orders.print-qr-3col', compact('purchaseOrder', 'qr_code_collection'));
    }

    public function processUpload(Request $request)
    {
        $categories = Category::active()->get();
//        $brands = Brand::orderBy('name')->get();
        $funds = Fund::pluck('name', 'id');

        $vendor = User::where('id', $request->get('vendor_id'))->first();
//dd($vendor);
        $origin_license = License::with('license_type')->find($request->get('origin_license_id'));
        $destination_license = License::with('license_type')->find($request->get('destination_license_id'));

        $customer_type = $request->get('customer_type');
        $txn_date = Carbon::parse($request->get('txn_date'));
        $terms = $request->get('terms');
        $fund = Fund::find($request->get('fund_id'));
        $manifest_no = $request->get('manifest_no');

        //get uploaded file packages
        $path = $request->file('packages')->getRealPath();

        $packages = collect(array_map('str_getcsv', file($path)));

//dd($packages);
//        $csv_data = array_slice($data, 0, 2);

        return view('purchase_orders.review-upload', compact(
            'categories',
//            'brands',
            'funds',
            'vendor',
            'origin_license',
            'destination_license',
            'customer_type',
            'txn_date',
            'terms',
            'fund',
            'packages',
            'manifest_no'
        ));
    }

    public function retag(Request $request, PurchaseOrder $purchaseOrder)
    {

        if($request->isMethod('post'))
        {
            $tag_id = $request->get('tag_id');
            $produce_lbs = $request->get('create_pounds');

//        dump($tag_id);

            foreach($purchaseOrder->batches as $batch) {

                if($batch->units_purchased != $batch->inventory) continue;
                if( ! in_array($batch->uom, ['lb','g'])) continue;

                $uid = config('highline.metrc_tag')[$purchaseOrder->destination_license_id].str_pad( (int)$tag_id, 9, 0, STR_PAD_LEFT);

                $used_weight = ($batch->uom == 'g') ? $batch->inventory : $batch->inventory * config('highline.uom.lb');
//            $qty_to_xfer = ($batch->uom == 'lb') ? $batch->inventory : $batch->inventory / config('highline.uom.lb');
                $qty_to_xfer = $batch->inventory;

//                $transfer_log_data = [
//                    'user_id' => Auth::user()->id,
//                    'batch_id' => $batch->id,
//                    'quantity_transferred' => $qty_to_xfer,
//                    'start_wt_grams' => $used_weight,
//                    'packer_name'=>'System',
//                ];
//
////            dd($transfer_log_data);
//
//                $transfer_log = new TransferLog($transfer_log_data);

//            dump($transfer_log);

                //amount
                $amount = $batch->inventory;
                $uom = $batch->uom;
                if($produce_lbs && $batch->uom == 'g') {
                    $amount = $batch->inventory / config('highline.uom.lb');
                    $uom = 'lb';
                }

                $packages_created = [
                    [
                        "ref_number"=>$uid,
                        "category_id" => $batch->category_id,
                        "brand_id" => null,
                        "amount" => $amount,
                        "uom" => $uom,
                        "packed_date" => Carbon::today(),
                        "fund_id" => $batch->fund_id,
                    ]
                ];

//            dump($packages_created);

                $transfer_resp = $batch->transfer(
                    $used_weight,
                    $qty_to_xfer,
                    $packages_created,
                    $batch->name
                );

                if($transfer_resp instanceof QueryException) {
                    flash()->error($transfer_resp->getMessage());

//                    return back(route('batches.transfer', $batch->ref_number))
                    return redirect(route('purchase-orders.show', $purchaseOrder->id))
//                    ->withErrors($e->getMessage())
                        ->withInput($request->all());
                }
                $tag_id++;

                $batch->inventory = (float)bcsub($batch->inventory, $batch->inventory, 4);
                $batch->transfer = (float)bcadd($batch->inventory, $batch->inventory, 4);

                $batch->save();

            }

            return redirect(route('purchase-orders.retag', $purchaseOrder->id));

        }
        $purchaseOrder->load(
            'vendor',
            'customer',
            'batches.children_batches.parent_batch.transfer_log',
            'batches.children_batches.order_details.sale_order.destination_license',
            'batches.children_batches.order_details.order_detail_returned',
            'batches.children_batches.order_details.batch',
            'batches.brand',
            'batches.category',
            'batches.cultivator',
            'batches.fund',
            'batches.order_details_accepted',
            'fund',
            'destination_license.license_type');
//        dd($purchaseOrder);

//        $b = Batch::find(12512);
//        dd($b->toJson());
//dd($b->available_weight_grams);

        return view('purchase_orders.retag', compact('purchaseOrder'));

    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update($request->all());
        return response()->json($purchaseOrder);
    }

    public function updateBatch(Request $request, PurchaseOrder $purchaseOrder, Batch $batch)
    {
        $purchaseOrder->load('transactions');

        if($batch->order_details->isNotEmpty()) {
            flash()->error('Unable to update batch# '.$purchaseOrder->ref_number);
            return redirect(route('purchase-orders.show', $purchaseOrder->id));
        }

        try {

            if((float)$request->units_purchased === 0.0) {
                $batch->delete();
            } else {

                if($batch->units_purchased == $batch->inventory) {
                    $batch->inventory = $request->units_purchased;
                }

                $batch->units_purchased = $request->units_purchased;

                $batch->unit_price = $request->unit_price;
                $batch->subtotal_price = $request->units_purchased * $request->unit_price;
                $batch->calculateCultTax();
                $batch->save();
            }

            $purchaseOrder->load('batches');

            $purchaseOrder->updateTotals();

            flash()->success('Purchase Order #'.$purchaseOrder->ref_number.' Updated');

            return redirect(route('purchase-orders.show', $purchaseOrder->id));

        } catch(\Exception $e) {

            flash()->error("Unable to update: ".$e->getMessage());

            return redirect(route('purchase-orders.show', $purchaseOrder->id));

        }

    }

    public function payment(PurchaseOrder $purchaseOrder)
    {
        $payment = request('payment');
        $txn_date = request('txn_date');
        $payment_method = request('payment_method');
        $ref_number = request('ref_number');
        $memo = request('memo');

//        if($payment > $purchaseOrder->balance) {
//            flash()->error('Invalid payment');
//            return redirect(route('purchase-orders.show', $purchaseOrder->id));
//        }

        $purchaseOrder->applyPayment($payment, $txn_date, $payment_method, $ref_number, $memo);

        flash()->success('Payment applied');
        return redirect(route('purchase-orders.show', $purchaseOrder->id));

    }

    public function resetFilters(PurchaseOrderFilters $purchaseOrderFilters)
    {
        $purchaseOrderFilters->resetFilters();
        return redirect(route('purchase-orders.index'));
    }

}
