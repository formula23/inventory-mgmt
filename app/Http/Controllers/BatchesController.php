<?php

namespace App\Http\Controllers;

use App\Batch;
use App\Brand;
use App\Broker;
use App\Category;
use App\Cultivator;
use App\Filters\BatchFilters;
use App\Fund;
use App\License;
use App\Repositories\Contracts\SaleOrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\DbSaleOrderRepository;
use App\Repositories\DbUserRepository;
use App\SaleOrder;
use App\TransferLog;
use App\User;
use App\VaultLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BatchesController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        view()->share('title','Inventory');

    }

    public function search()
    {

        $query = request()->get('q');
        $batches = Batch::search($query)->orderBy('category_id')->get();

        $batch_results = collect();

        $batches->each(function($batch, $key) use ($batch_results) {

            $batch_results->push([
                "id"=>$batch->id,
                "name"=>$batch->category->name.": ".$batch->name.($batch->description?" (".$batch->description.")":""),

//                "label"=>implode(" ", [
//                        $batch->category->name.": ".$batch->name.($batch->description?" (".$batch->description.")":""),
//                        $batch->inventory." ".$batch->uom,
////                        ($batch->suggested_unit_sale_price?display_currency($batch->suggested_unit_sale_price):null),
//                        "<br><small>Cost: ".display_currency($batch->unit_price)."</small>"
//                ]),
                "sold_as_name"=>$batch->present()->branded_name(),
                "inventory" => $batch->inventory,
                "show_inventory"=>true,
                "uom" => $batch->uom,
                "unit_cost"=>display_currency($batch->unit_price, 2, 0,""),
                "pre_tax_unit_cost"=>display_currency($batch->pre_tax_cost),
                "suggested_unit_sale_price"=>display_currency($batch->suggested_unit_sale_price, 2, 0,""),
                "has_cult_tax"=>($batch->tax_rate_id?true:false),
                "cog"=>1,
                "vendor"=>($batch->vendor()?$batch->vendor()->name:""),
            ]);

        });
//dd($batch_results);
        $t = array(
            "status" => false,
            "error"  => null,
            "data"   => [
                "batches" => $batch_results
            ]
        );

        return response()->json($t);

    }

    public function index(BatchFilters $batchFilters)
    {
        if(Gate::denies('batches.index')) {
            flash()->error('Access Denied');
            return back();
        }

        $categories = Category::active()->get();
        $vendors = (new DbUserRepository)->vendors()->orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $funds = Fund::pluck('name', 'id');
        $my_licenses = License::system_licenses()->get();

        $batches = (new Batch)->currentInventory($batchFilters, [
            'purchase_order.vendor',
            'order_details',
            'category',
            'license.license_type',
            'parent_batch.purchase_order.vendor',
            'parent_batch.parent_batch.purchase_order.vendor',
            'parent_batch.parent_batch.parent_batch.purchase_order.vendor',
            'parent_batch.parent_batch.parent_batch.parent_batch.purchase_order.vendor',
            'child_batches',
            'source_batches',
            'transfer_pre_pack'
        ])->get();


//        $batches = Cache::rememberForever('batches'.Auth::user()->id, function() use ($batchFilters) {
//
//            return (new Batch)->currentInventory($batchFilters, [
//                'purchase_order.vendor',
//                'order_details',
//                'category',
//                'license.license_type',
//                'parent_batch.purchase_order.vendor',
//                'parent_batch.parent_batch.purchase_order.vendor',
//                'parent_batch.parent_batch.parent_batch.purchase_order.vendor',
//                'parent_batch.parent_batch.parent_batch.parent_batch.purchase_order.vendor',
//                'child_batches',
//                'source_batches',
//                'transfer_pre_pack'
//            ])->get();
//
//        });

        $total_inventory_value = (new Batch)->totalInventoryValue()->total_inventory_value;
//        $total_inventory_value = 0;

        $derived_inventory_value = (new Batch)->derivedInventoryValue();
//        $derived_inventory_value = 0;

        $filters = $batchFilters->getFilters()->toArray();

        return view('batches.index', compact(
            'batches',
            'filters',
            'vendors',
            'categories',
            'brands',
            'total_inventory_value',
            'derived_inventory_value',
            'funds',
            'my_licenses'
        ));
    }

    public function show(Batch $batch, User $selected_customer, VaultLog $vault_log_ref)
    {
        if(Gate::denies('batches.show')) {
            flash()->error('Access Denied');
            return back();
        }

        if($vault_log_ref->exists && is_null(Session::get('vault_log_ref'))) {
            Session::put('vault_log_ref', $vault_log_ref);
        }

        $cost_override = request('cost_override');

        $batch->load([
            'transfer_logs_prepack',
            'transfer_logs_reconcile',
            'source_batches',
            'created_batch',
            'vault_logs'=>function($q) {
                $q->orderBy('created_at', 'desc');
            },
            'vault_logs.user',
            'vault_logs.broker'
        ]);

        $open_sales_orders = SaleOrder::openOrders()->with('customer','broker', 'destination_license')->get();

        $sales_reps = User::salesReps()->orderBy('name')->get();

        $brokers = Broker::orderBy('name')->pluck('name','id');

        $customers = User::customers()->active()->orderBy('name')->get();

        $selected_customer->load(['license_types' => function ($q) {
            $q->orderBy('name');
        },
            'licenses.license_type']);

        $pending_sale_orders = (new DbSaleOrderRepository)->salesByBatchId($batch->id);

        return view('batches.show', compact(
            'batch',
            'open_sales_orders',
            'customers',
            'selected_customer',
            'sales_reps',
            'cost_override',
            'pending_sale_orders',
            'brokers'
        ));

    }

    public function edit(Batch $batch)
    {
        if(Gate::denies('batches.edit')) {
            flash()->error('Access Denied');
            return back();
        }
        $brands = Brand::orderBy('name')->get();
        $categories = Category::active()->get();

        $cultivators = Cultivator::all();

        $testing_laboratories = User::testingLaboratory()->get();

//dd($testing_laboratory);
        return view('batches.edit', compact('batch', 'categories', 'brands', 'cultivators','testing_laboratories'));
    }

    public function update(Request $request, Batch $batch)
    {
        if(Gate::denies('batches.edit')) {
            flash()->error('Access Denied');
            return back();
        }

        $data = request()->all();
        if($data['status']=='Lab') {
            $data['testing_status'] = 'In-Testing';
        }

        if( ! empty($data['testing_status']) && in_array($data['testing_status'], ['Passed','Failed'])) {
            $data['status'] = 'Inventory';
        }

        try {

            $batch->update($data);

        } catch(QueryException $e) {
            flash()->error($e->errorInfo[2]);
            return back()->withInput();
        } catch(\Exception $e) {
            flash()->error($e->getMessage());
            return back()->withInput();
        }

        flash()->success('Successfully updated '.$batch->name);

        return redirect(route('batches.show', $batch->ref_number));

    }

    public function labels(Batch $batch)
    {
        view()->share('title','Batch Labels');



        return view('batches.labels', compact('batch'));
    }

    public function transfer(Request $request, Batch $batch)
    {
        view()->share('title','Batch Transfer');

//        if( ! $batch->canTransfer()) {
//            flash()->error('Can only transfer bulk flower');
//            return redirect(route('batches.index'));
//        }

        $categories = Category::active()->get();
        $brands = Brand::orderBy('name')->get();
        $funds = Fund::pluck('name', 'id');

        if ($request->isMethod('post')) {

            $packages_created=[];
            for($i=0; $i < count($request->all()['rows']['ref_number']); $i++)
            {
                foreach($request->all()['rows'] as $field=>$vals) {
                    $packages_created[$i][$field] = $vals[$i];
                }
            }

            $qty_to_xfer = $request->get('transfer_qty', 0);
            $packer_name = $request->get('packer_name');
            $start_weight = $request->get('start_weight', 0);
            $used_weight = $request->get('used_weight', 0);
            $used_weight_uom = $request->get('used_weight_uom', 0);
            $remaining_weight = $request->get('remaining_weight', 0);
            $product_name = $request->get('name');
//            $packages_created = $request->get('rows');

            if( ! $used_weight) {
                $used_weight = ($start_weight - $remaining_weight);
            }

            if(empty($qty_to_xfer)) {
                if($batch->uom=='lb' && $used_weight_uom=='g') {
                    $qty_to_xfer = $used_weight / config('highline.uom.lb');
                } elseif($batch->uom=='g' && $used_weight_uom=='lb') {
                    $qty_to_xfer = $used_weight * config('highline.uom.lb');
                    $used_weight = $qty_to_xfer;
                } elseif($batch->uom=='lb' && $used_weight_uom=='lb') {
                    $qty_to_xfer = $used_weight;
                    $used_weight = $used_weight * config('highline.uom.lb');
                } else {
                    $qty_to_xfer = $used_weight;
                }
            }

//            dump('qty to xfer');
//            dump($qty_to_xfer);
//
//            dump('start weight');
//            dump($start_weight);
//
//            dump('remaining weight');
//            dump($remaining_weight);
//
//            dump('used weight');
//            dump($used_weight);
//
            $available_inv = ($batch->wt_grams?:$batch->inventory);
//
//            dump('available inv:');
//            dump($available_inv);
//
//            dd(bccomp($qty_to_xfer, $available_inv, 4));

            if(! $qty_to_xfer || bccomp($qty_to_xfer, $available_inv, 4) > 0)
            {
                flash()->error('Convert quantity cannot exceed available quantity'.$qty_to_xfer."--".$available_inv);
                return redirect(route('batches.transfer', $batch->ref_number))
                    ->withInput($request->all());
            }
            else {

//dump($used_weight);
//dump($qty_to_xfer);
//dump($packages_created);
//dump($product_name);
//dd('end');
                try {

                    $batch->transfer(
                        $used_weight,
                        $qty_to_xfer,
                        $packages_created,
                        $packer_name,
                        $product_name
                    );

                    if($batch->wt_grams) {
                        $batch->wt_grams = (float)bcsub($batch->wt_grams, $qty_to_xfer, 4);
                        $batch->unit_price = (float)bcsub($batch->unit_price, $batch->total_converted_cost, 4);

                        if($batch->wt_grams <= 0) $batch->inventory=0;
                        if($batch->unit_price < 0) { //
//                        (new TransferLog)->storePackagingLoss($batch);
                            $batch->unit_price=0;
                        }
//                    dd($batch);
                    } else {
                        $batch->inventory = (float)bcsub($batch->inventory, $qty_to_xfer, 4);
//                    $batch->transfer = (float)bcadd($batch->transfer, $qty_to_xfer, 4);
                    }

                    $batch->save();

                    return redirect(route('batches.transfer-log', $batch->ref_number));

                } catch(\Exception $e) {
                    DB::rollBack();
//                    dd($e);
                    flash()->error($e->getMessage());
                    return redirect()->back();
                }

            }

        }

        return view('batches.transfer', compact('batch','categories', 'brands', 'funds'));
    }

    public function transfer_log(Batch $batch, TransferLog $transferLog)
    {
        view()->share('title','Batch Transfer Log');

        $batch->load(['transfer_logs' => function ($query) {
            $query->where('type','Pre-Pack')->orderBy('created_at', 'desc')
                ->with(['transfer_log_details.batch_created','user', 'batch_converted']);
        }]);

        if (request()->isMethod('post')) {

            try {
                $exitCode = Artisan::call('fix:reverse_prepack', [
                    'transfer_log_id' => $transferLog->id,
                    '--no-interaction' => true,
                ]);

//            dd($exitCode);
//            dd(Artisan::output());
//
            } catch(\Exception $e)
            {
                flash()->error($e->getMessage());
                return redirect(route('batches.transfer-log', $batch->ref_number));
            }

            return redirect(route('batches.transfer-log', $batch->ref_number));

        }

        return view('batches.transfer-log', compact('batch', 'transferLog'));
    }

    public function pickup(Batch $batch)
    {
        if(Gate::denies('batches.pickup')) {
            flash()->error('Access Denied');
            return back();
        }

        if(bccomp(request('pickup_qty'), $batch->inventory, 2) === 1) {
            flash()->error('Not allowed to pickup more than: '.$batch->inventory);
            return redirect(route('batches.show', $batch->ref_number));
        }

        $batch->pickup(request('pickup_qty'));

        return redirect(route('batches.show', $batch->ref_number));

    }

    public function sell(Request $request, Batch $batch)
    {
        if(Gate::denies('batches.sell')) {
            flash()->error('Access Denied');
            return back();
        }

        $vault_log_ref = Session::get('vault_log_ref');

        $customer = User::find(request('customer_id', request('destination_user_id')));

        $customer_id = ($customer?$customer->id:0);
        $bill_to_id = (request('bill_to_id')?request('bill_to_id'):null);
        $sales_rep_id = request('sales_rep_id');
        $broker_id = request('broker_id');
        $sale_order_id = request('sale_order_id');
        $destination_license_id = request('destination_license_id');

        $txn_date = request('txn_date');
        $expected_delivery_date = request('expected_delivery_date', Carbon::now());
        $terms = request('terms');
        $customer_type = request('customer_type');
        $add_sample = request('add_sample');
        $sale_type = request('sale_type');
        $notes = request('notes');

        if( ! $customer_id && ! $sale_order_id) {
            flash()->error('Please select a customer or sale order.');
            return redirect(route('batches.show', $batch->ref_number));
        }

        $quantity = request('sell_units');

        if($quantity <= 0 || bccomp($quantity,$batch->inventory) === 1) {
            flash()->error('Please check the quantity trying to sell.');
            return redirect(route('batches.show', $batch->ref_number));
        }

        $saleOrder = new DbSaleOrderRepository();

        if($customer) {
            $sale_order = $saleOrder->create(compact('customer', 'txn_date', 'expected_delivery_date',
                'customer_type', 'sales_rep_id', 'bill_to_id', 'broker_id', 'sale_type', 'terms', 'destination_license_id', 'notes'));
        } else {
            $sale_order = $saleOrder->find($sale_order_id);
        }

        try {

            DB::beginTransaction();

            if($request->get('cost_markup')) {
                $sale_price = $batch->unit_price + $request->get('cost_markup');
            } else if($sale_price_pre_tax = $request->get('pre_tax_sale_price')) {

                $cult_tax=0;

                if(in_array($batch->category_id, [1,24])) { //bulk flower
                    $cult_tax = config('highline.cultivation_tax.flower.'.$batch->uom);
                } else if($batch->category_id == 6) { //trim
                    $cult_tax = config('highline.cultivation_tax.trim.'.$batch->uom);
                }
                $sale_price = $sale_price_pre_tax + $cult_tax;
            } else {
                $sale_price = request('sale_price');
            }

            $sale_order->addUpdateItem($batch, request('sold_as_name'), $quantity, $sale_price);

            $redirect_vault_log=false;
            if($vault_log_ref) {
                $redirect_vault_log=true;
                $vault_log_ref->order_detail_id = $sale_order->latest_order_detail->id;
                $vault_log_ref->save();
                Session::forget('vault_log_ref');
            }

//        $batch->inventory -= $quantity;

            //// add sample
//        if($add_sample) {
//            if($batch->inventory > 0) {
//                $batch->inventory--;
//                $sale_order->addUpdateItem($batch, request('sold_as_name')." (Sample)", 1, '0.50');
//            } else {
//                flash()->error('Not enough inventory to add a sample also!');
//            }
//        }

//            $batch->save();
            $sale_order->calculateTotals();

            DB::commit();

            flash()->success($batch->name.' added to sale order');

        } catch(QueryException $e) {
            DB::rollBack();
            if($e->getCode() == 22003) {
                flash()->error('Unable to add item to order. Review UOM and sale price.');
            } else {
                flash()->error($e->getMessage());
            }
        }

        if($redirect_vault_log) {
            return redirect(route('vault-logs.index'));
        } else {
            return redirect(route('sale-orders.show', $sale_order->id));
        }
    }

    public function release(Batch $batch)
    {
        if(Gate::denies('batches.release')) {
            flash()->error('Access Denied');
            return back();
        }

        $quantity = request('release_units');
        $batchPickup = $batch->myPickupInTransit;

        if($quantity > $batchPickup->units) {
            flash()->error('Can not return this many.');
            return redirect(route('batches.show', $batch->ref_number));
        }

        $batchPickup->release($quantity);
        $batch->release($quantity);

        return redirect(route('batches.show', $batch->ref_number));
    }

    public function sales(Batch $batch)
    {
        $title = 'Sale Orders';

        $sale_orders = (new DbSaleOrderRepository)->salesByBatchId($batch->id);

        return view('batches.sales', compact('sale_orders','title', 'batch'));

    }

    public function qrCode(Batch $batch)
    {

        return view('batches.print-qr-lg', compact('batch'));
    }


    public function qrCodes(Category $category)
    {
        $category->load(['batches' => function ($query) {
            $query->where('batches.inventory','>', 0);
        }]);

//        dd(->get());
//        if(Gate::denies('po.printqr')) {
//            return redirect(route('dashboard'));
//        }

//        $batches = Batch::filters($batchFilters)->orderBy('name')->get();
//dd($batch);
//        $qr_code_collection = new Collection();
//        foreach($batches as $batch) {
//
//            $gofor=($batch->uom == 'lb' ? round($batch->inventory) : 6);
//            for($i=1; $i<=$gofor; $i++) {
//                $qr_code_collection->push($batch);
//            }
//        }
//dd($qr_code_collection);
        return view('batches.print-qrs-lg', compact('category'));
    }

    public function resetFilters(BatchFilters $batchFilters)
    {
        $batchFilters->resetFilters();
        return redirect(route('batches.index'));
    }

    public function printInventory(BatchFilters $batchFilters, $remove_cost=0)
    {

        $batches = (new Batch)->currentInventory($batchFilters, [
            'purchase_order.vendor',
            'parent_batch.purchase_order.vendor',
            'parent_batch.parent_batch.purchase_order.vendor',
            'parent_batch.parent_batch.parent_batch.purchase_order.vendor',
            'parent_batch.parent_batch.parent_batch.parent_batch.purchase_order.vendor',
            'category',
            'brand'
        ])->get();

        return view('batches.print-inventory', compact('batches', 'remove_cost'));
    }


    public function reconcile(Batch $batch)
    {
        view()->share('title','Inventory / Reconcile');

        $q = Batch::select('batches.*')
            ->join('categories', 'batches.category_id', '=', 'categories.id')
            ->with(['category', 'purchase_order', 'brand'])

            ->orderBy('categories.id');

        if($batch->exists) {
            $q->where('batches.id', $batch->id);
        } else {
            $q->where('inventory','>',0);
        }

        $batches = $q->get();

        return view('batches.reconcile', compact('batches'));
    }

    public function reconcileProcess(Request $request)
    {

        if($request->has('adjustment_file'))
        {
            //get uploaded file packages
            $path = $request->file('adjustment_file')->getRealPath();
            $adjustment_file = collect(array_map('str_getcsv', file($path)));

            foreach($adjustment_file as $batch_adjustment)
            {

                $batch = Batch::where('ref_number', $batch_adjustment[0])->first();
                if(empty($batch) || $batch->inventory <= 0) continue;

                $batch_inventory = ($batch->wt_based ? $batch->wt_grams : $batch->inventory );

                if($batch->wt_based) {
                    $new_value = bcadd($batch->wt_grams, (float)$batch_adjustment[1], 4);
                } else {
                    $new_value = bcadd($batch->inventory, (float)$batch_adjustment[1], 4);
                }
//
//                if($new_value != 0) {
//                    dump($batch->id);
//                    dump($batch->uom." | ".config('highline.uom')[$batch->uom]." - ".$batch->inventory." -- ". (float)$batch_adjustment[1]." -- ". $new_value);
//
//                    if($new_value < 0) {
//                        dump('***********************************');
//                    }
//                }

                 $batch->reconcile($new_value, $batch_inventory, $batch_adjustment[3], null);

            }
//            dd('end');
        }


        if($request->has('batch'))
        {
            foreach($request->get('batch') as $batch_id => $batch_values) {

                if($batch_values['new_value'] < 0) continue; //can't be less 0 - ignore

                if(round($batch_values['current_value'], 4) != round($batch_values['new_value'], 4)) {

                    $batch = Batch::find($batch_id);

                    $batch_inventory = ($batch->wt_based ? $batch->wt_grams : $batch->inventory );

                    if(round($batch_inventory) != round($batch_values['current_value'])) continue; //something changed

                    $batch->reconcile($batch_values['new_value'], $batch_values['current_value'], $batch_values['reason'], $batch_values['notes']);

                }
            }
        }

        return redirect(route('batches.reconcile'));

    }

    public function reconcileLog(Request $request, Batch $batch)
    {

        $qry = TransferLog::where('type','Reconcile')
            ->with('user', 'batch_converted')
            ->orderBy('created_at','desc');

        if($batch->exists) {
            $qry->where('batch_id', $batch->id);
        }

        $reconcile_logs = $qry->paginate(25);

        return view('batches.reconcile-log', compact('reconcile_logs'));
    }

    public function reconcileLogShow(TransferLog $transferLog)
    {
        $transferLog->load(['batch_converted','transfer_log_details.batch_created']);

        return view('batches.reconcile-log-show', compact('transferLog'));


    }

    public function submit_for_testing(Request $request, Batch $batch)
    {

        if ($request->isMethod('post')) {

            $sample_weight_grams = $request->get('sample_weight', 0);
            $ref_number = $request->get('ref_number');
            $packaged_date = $request->get('packaged_date');
            $testing_laboratory_id = $request->get('testing_laboratory_id');

            if(empty($sample_weight_grams) || empty($ref_number)) {
                flash()->error('Sample weight or METRC/UID Required');
                return back()->withInput($request->all());
            }

            $sample_batch = $batch->submitForTesting($sample_weight_grams, $ref_number, $packaged_date, $testing_laboratory_id);
            $sample_batch->reconcile(0, null, 'Lab Test Sample');

            try {

            } catch(QueryException $e) {
//dd($e);
            }

            flash()->success('Test Sample Batch Submitted!');

            return redirect(route('batches.show', $batch->ref_number));
        }

        $testing_laboratories = User::testingLaboratory()->pluck('name','id');

        $batch->load(['category']);

        return view('batches.submit_for_testing', compact('batch','testing_laboratories'));
    }

    public function testing_results(Request $request, Batch $batch)
    {
        if(empty($request->testing_status) ||
            empty($request->coa_link) ||
            empty($request->thc) ||
            empty($request->cbd)
        ) {
            flash()->error('Testing Status, Link, THC & CBD are all required.');
            return back();
        }
//dd($request->all());
        $batch->update($request->all());

        flash()->success('Test results saved!');

        return redirect(route('batches.show', $batch->ref_number));
    }
}
