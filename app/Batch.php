<?php

namespace App;

use App\Presenters\PresentableTrait;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Batch extends Model
{
    use PresentableTrait;

    protected $guarded = [];

    protected $dates = ['cultivation_date', 'packaged_date', 'tested_at'];

    protected $casts = [
        'character' => 'array',
    ];

    protected $appends = ['cost'];

    public $total_converted_cost = 0;
    public $total_converted_grams = 0;
    public $shortage_cost = 0;
    public $shortage_grams = 0;
    protected $available_weight_grams = 0;

    protected $presenter = 'App\Presenters\Batch';

    public static function boot()
    {
        parent::boot();

        static::saving(function() {
//            dd('saving');
//            Cache::forget('fulfilled_wish');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parent_batch()
    {
        return $this->belongsTo(Batch::class, 'parent_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function cultivator()
    {
        return $this->belongsTo(User::class, 'cultivator_id');
    }

    public function testing_laboratory()
    {
        return $this->belongsTo(User::class, 'testing_laboratory_id');
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function license()
    {
        return $this->belongsTo(License::class);
    }

    public function tax_rate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function child_batches()
    {
        return $this->hasMany(Batch::class, 'parent_id');
    }

    public function children_batches()
    {
        return $this->hasMany(Batch::class, 'parent_id')->with('children_batches.order_details_cog.sale_order.customer');
    }

    public function source_batches()
    {
        return $this->hasMany(Batch::class, 'child_id');
    }

    public function created_batch()
    {
        return $this->belongsTo(Batch::class, 'child_id');
    }

    public function transfer_log()
    {
        return $this->hasOne(TransferLog::class);
    }

    public function transfer_logs()
    {
        return $this->hasMany(TransferLog::class);
    }

    public function transfer_logs_prepack()
    {
        return $this->transfer_logs()->where('type', 'Pre-Pack');
    }

    public function transfer_logs_reconcile()
    {
        return $this->transfer_logs()->where('type', 'Reconcile');
    }

    public function transfer_pre_pack()
    {
        return $this->transfer_logs_prepack();
    }

    public function transfer_log_detail()
    {
        return $this->hasOne(TransferLogDetail::class);
    }

    public function transfer_log_details()
    {
        return $this->hasMany(TransferLogDetail::class);
    }

    public function vault_logs()
    {
        return $this->hasMany(VaultLog::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function pickups()
    {
        return $this->hasMany(BatchPickup::class);
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function order_details_cog()
    {
        return $this->hasMany(OrderDetail::class)->where('cog', 1);
    }

    public function order_details_not_accepted()
    {
        return $this->order_details()->whereNull('units_accepted');
    }

    public function order_details_accepted()
    {
        return $this->order_details()->where('units_accepted', '>', 0);
    }

    public function myPickups()
    {
        return $this->pickups()->where('user_id', Auth::user()->id);
    }

    public function hasPickups()
    {
        return (bool)$this->pickups()->count();
    }

    public function getOrderDetailsCogSumMarginAttribute()
    {
        return $this->order_details_cog->sum('margin');
    }

    public function getCostAttribute()
    {
        return ($this->units_purchased * $this->unit_price);
    }

    public function getTaxLiabilityAttribute()
    {
        return $this->inventory * $this->unit_tax_amount;
    }

    public function getPreTaxCostAttribute()
    {
        return $this->unit_price - $this->unit_tax_amount;
    }

    public function getCOASourceBatchAttribute()
    {
        try {
            if($this->coa_batch) return $this;
            $batch=$this;
            do {
//                dump($batch->id);
//                dump(is_null($batch->parent_batch));
//                dump($batch->coa_batch);
                if($batch->coa_batch) return $batch;
                if(!is_null($batch->parent_batch)) {
                    $batch = $batch->parent_batch;
                }

            } while(!empty($batch->parent_id) && !$this->parent_batch->coa_batch);
        } catch (\Exception $e) {
            dd($e);
        }
//        dump('end');
        if(!$batch->coa_batch) $batch=null;
//        dump($batch?$batch->id:'');
        return $batch;
    }

    public function getTopLevelParentAttribute()
    {
        try {
            $batch=$this;
            do {
                if(!is_null($batch->parent_batch)) {
                    $batch = $batch->parent_batch;
                }
            } while(!empty($batch->parent_id));
        } catch (\Exception $e) {
            dd($e);
        }

        return $batch;
    }

    public function vendor()
    {
        if($this->topLevelParent) {
            return $this->topLevelParent->purchase_order->vendor;
        } else {
            return null;
        }
    }

    public function myPickupInTransit()
    {
        return $this->hasOne(BatchPickup::class)
            ->where('user_id', Auth::user()->id);
    }

    public function scopeWithInventory($query)
    {
        return $query->where('inventory', '>', 0);
    }

    public function scopeFilters($query, $filters)
    {
        return $filters->apply($query);
    }

    public function getAddedToInventoryAttribute()
    {
        $date = $this->created_at;
        if($this->purchase_order) $date = $this->purchase_order->txn_date;

        if($date->isToday()) {
            return "Today";
        } else {
            return $date->diffForHumans();
        }
    }

    public function getAddedToInventoryDateAttribute()
    {
        $date = $this->created_at;
        if($this->purchase_order) $date = $this->purchase_order->txn_date;

        return $date->format('m/d/Y');
    }

    public function getStatusAttribute($value)
    {
        return $value;
//        if($value=='failed') return $value;
//        return ($this->inventory > 0 || $this->transit > 0) ? 'inventory' : 'sold' ;
    }

    public function getHarvestDateAttribute()
    {
        if(!$this->top_level_parent->cultivation_date) return '--';
        return $this->top_level_parent->cultivation_date->format('m/d/Y');
    }

    public function getRevenueAttribute()
    {
        $revenue = $this->order_details->sum('revenue');

        if($this->children_batches->count()) {
            $this->loopBatches($this->children_batches, 'revenue', $revenue);
        }

        return $revenue;
    }

    public function getAvailableWeightGramsAttribute()
    {
        $available_wt = ($this->inventory * config('highline.uom')[$this->uom]);

        if($this->children_batches->count()) {
            $this->loopBatches($this->children_batches, 'inventory', $available_wt);
        }
        return $available_wt;
    }

    public function getAvailableWeightPoundsAttribute()
    {
        return $this->getAvailableWeightGramsAttribute() / config('highline.uom.lb');
    }

    protected function loopBatches($child_batches, $key, &$val)
    {
        foreach($child_batches as $child_batch)
        {
            $val += $child_batch->{$key} * config('highline.uom')[$child_batch->uom];

            if($child_batch->children_batches->count()) {
                $this->loopBatches($child_batch->children_batches, $key, $val);
            }
        }
    }



    public function getPackagedWeightGramsAttribute()
    {
        return $this->transfer_logs->sum('start_wt_grams');
    }

    public function getPackagedWeightPoundsAttribute()
    {
        return number_format($this->transfer_logs->sum('start_wt_grams') / config('highline.uom.lb'), 4);
    }

    public function getWeightAcceptedGramsAttribute()
    {
        $weight_grams = $this->order_details->sum('weight_accepted_grams');

//        if($this->children_batches->count()) {
//            $this->loopBatchesOrderDetailsSum($this->children_batches, 'weight_grams_accepted', $weight_grams);
//        }
        return $weight_grams;
    }

    public function getWeightAcceptedPoundsAttribute()
    {
        return number_format($this->getWeightAcceptedGramsAttribute() / config('highline.uom.lb'), 4);
    }

    public function getWeightPendingGramsAttribute()
    {
        $weight_grams = $this->order_details->sum('weight_pending_grams');

//        if($this->children_batches->count()) {
//            $this->loopBatchesOrderDetailsSum($this->children_batches, 'weight_grams_pending', $weight_grams);
//        }
        return $weight_grams;
    }

    public function getWeightPendingPoundsAttribute()
    {
        return number_format($this->getWeightPendingGramsAttribute() / config('highline.uom.lb'), 4);
    }

    protected function loopBatchesOrderDetailsSum($child_batches, $key, &$val)
    {
        foreach ($child_batches as $child_batch) {
            $val += $child_batch->order_details->sum($key);

            if ($child_batch->children_batches->count()) {
                $this->loopBatchesOrderDetailsSum($child_batch->children_batches, $key, $val);
            }
        }
    }

    public function getUnitsPurchasedGramsAttribute()
    {
        return number_format($this->units_purchased * config('highline.uom')[$this->uom], 4);
    }

    public function getRequiresRetagAttribute()
    {
//        return 0;
//        return ($this->order_details_cog()->where('units','>=',1)->count() > 1) || $this->inventory > 0;

//        dump($this->order_details_cog()->where('units','>=', 1)->count());
//        return ($this->with(['order_details_cog' => function ($query) {
//            $query->where('units','>=',1);
//            }]) || $this->inventory > 0);
//
//        dump($this->order_details_cog->pluck('units_accepted'));
//        if($this->id == 13620) {
//            dump($this);
//            dump($this->order_details_cog()->where('units','>=',1)->count());
//            dd($this->whereHas('order_details_cog', function ($qry) {
//                return $qry->where('units','>=',1);
//            })->get());
//        }
//
//
//        return ($this->inventory > 0);
    }

    public function inTesting()
    {
        return ($this->testing_status == 'In-Testing');
    }

    public function passedTesting()
    {
        return ($this->testing_status == 'Passed');
    }

    public function canCreatePackages()
    {
        return $this->canTransfer() && $this->testing_status != 'In-Testing';
    }

    public function canTransfer()
    {
        return ($this->inventory > 0);
        return ($this->top_level_parent->testing_status == 'Passed');
        return (in_array($this->category_id, [1,20]) && $this->inventory);
    }

//    public function cultivation_tax()
//    {
//        $this->tax = $this->calculateCultTax();
//        $this->save();
//
//        return $this;
//    }
//
//    public function cult_tax_amount()
//    {
//        if( ! $this->cost_includes_cult_tax) return 0;
//
//        if($this->tax_rate) { // tax rate based on tax rate table
//
//            $tax_rate_quantity = $this->units_purchased;
//
//            if($this->uom != $this->tax_rate->uom) {
//                $conv_rate = Conversion::getRate($this->uom, $this->tax_rate->uom);
//                if(!$conv_rate) {
//                    throw new \Exception("No conversion rate not found. From:".$this->uom." - To:".$this->tax_rate->uom);
//                }
//                $tax_rate_quantity = $this->units_purchased * $conv_rate->value;
//            }
//
//            return $tax_rate_quantity * $this->tax_rate->amount;
//
//        } else { //tax rate based on category
//
////            dump($this->tax_rate);
//
//////        $tax_rate = ($this->cultivation_date && $this->cultivation_date->year < 2020 ? 'cultivation_tax_2019' : 'cultivation_tax' );
//
//            $grams = $this->units_purchased * config('highline.uom.'.$this->uom);
//            $ounces = ($grams / config('highline.uom.lb') * config('highline.conversions.oz_per_lb'));
//            switch ($this->category_id)
//            {
//                case 1: //flower
//                case 20: //smalls
//                case 33: //frost
//                case 36: //bulk samples
//                    return $ounces * config('highline.cultivation_tax.flower.ounce');
//                    break;
//                case 6: //trim
//                    return $ounces * config('highline.cultivation_tax.trim.ounce');
//                    break;
//                default:
//                    return 0;
//                    break;
//            }
//            return 0;
//
//        }
//
//    }

    public function pickup($quantity)
    {
        $this->inventory = bcsub($this->inventory, $quantity, 2);
        $this->transit = bcadd($this->transit, $quantity, 2);

        $this->addPickup($quantity);
        $this->save();
        return $this;
    }

    public function revertMyPickup($quantity)
    {
        $this->status = 'inventory';
        $this->transit = bcadd($this->transit, $quantity, 2);
        $this->sold = bcsub($this->sold, $quantity, 2);

        $this->addPickup($quantity);
        $this->save();
        return $this;
    }

    public function addPickup($quantity)
    {
        if($batch_pickup = $this->myPickupInTransit) {
            $batch_pickup->units = bcadd($batch_pickup->units, $quantity, 2);
            $batch_pickup->save();
        } else {
            $batch_pickup = new BatchPickup();
            $batch_pickup->user_id = Auth::user()->id;
            $batch_pickup->units = $quantity;
            $this->pickups()->save($batch_pickup);
        }
        return $this;
    }

    public function sell($quantity)
    {
//        $this->transit = bcsub($this->transit, $quantity, 2);
//        $this->sold = bcadd($this->sold, $quantity, 2);

        if($this->inventory==0) {
            $this->status = 'sold';
        }

        $this->save();
        return $this;
    }

    public function release($quantity)
    {
        $this->inventory = bcadd($this->inventory, $quantity, 4);
        $this->transit = bcsub($this->transit, $quantity, 4);
        $this->save();
        return $this;
    }

    public function calculateCultTax()
    {

        $this->tax = 0;
        $this->unit_tax_amount = 0;

        if( ! empty($this->tax_rate))
        {
            $tax_rate_quantity = $this->inventory;
            $this->unit_tax_amount = $this->tax_rate->amount;

            if($this->tax_rate->uom != $this->uom)
            {
                $conv_rate = Conversion::getRate($this->uom, $this->tax_rate->uom);
                if(!$conv_rate) {
                    throw new \Exception("No conversion rate not found. From:".$this->uom." - To:".$this->tax_rate->uom);
                }
                $tax_rate_quantity = ($this->inventory * $conv_rate->value);
                $this->unit_tax_amount = $this->tax_rate->amount * $conv_rate->value;
            }

            $this->tax = ($tax_rate_quantity * $this->tax_rate->amount);
        }
        else
        {
//            $grams = $this->units_purchased * config('highline.uom.'.$this->uom);
//            $ounces = ($grams / config('highline.uom.lb') * config('highline.conversions.oz_per_lb'));
//            switch ($this->category_id)
//            {
//                case 1: //flower
//                case 20: //smalls
//                case 33: //frost
//                case 36: //bulk samples
//                    $this->tax = $ounces * config('highline.cultivation_tax.flower.ounce');
//                    break;
//                case 6: //trim
//                    $this->tax = $ounces * config('highline.cultivation_tax.trim.ounce');
//                    break;
//                default:
//                    return 0;
//                    break;
//            }
            return 0;
        }
    }

    public function transfer($start_weight, $qty_to_xfer, $packages_created, $packer_name='System', $new_batch_name = null)
    {
        // --> $used_weight  -> $start_weight
        // --> $qty_to_xfer  -> $qty_to_xfer


        $transfer_log_data = [
            'user_id' => Auth::user()->id,
            'batch_id' => $this->id,
            'quantity_transferred' => $qty_to_xfer,
            'start_wt_grams' => $start_weight,
            'packer_name'=>$packer_name,
        ];

//        dump($start_weight);

        $xfer_log = new TransferLog($transfer_log_data);


        if($this->wt_based) {
            $g_price = $this->subtotal_price / config('highline.uom')[$this->uom];
        } else {
            $g_price = $this->unit_price / config('highline.uom')[$this->uom];
        }

//        dd($g_price * config('highline.uom')[$this->uom]);

        if(empty($start_weight)) {
            $start_weight = $qty_to_xfer * config('highline.uom')[$this->uom];
        }

//        dump("start wt");
//        dump($start_weight);
//        dump('end');
//
//        dump('qty to xfer');
//        dump($qty_to_xfer);
//
//        dump($g_price);
//        dd('d');

        $total_cost = ($g_price * $start_weight);
//        dump('total cost');
//        dump($total_cost);
        $total_grams = $start_weight;

        if( ! $this->wt_grams) {
            $this->shortage_cost = ($qty_to_xfer * $this->unit_price) - $total_cost;
            $this->shortage_grams = ($qty_to_xfer * config('highline.uom')[$this->uom]) - $start_weight;
        }

        DB::beginTransaction();

        $new_batches_created = [];
//dd($packages_created);
        foreach($packages_created as $idx=>$row) {

            if(is_null($row['category_id']) ||
                is_null($row['amount']) ||
                is_null($row['uom'])) continue;

            $grams = get_grams($row['uom']);

            $unit_price = round($g_price * $grams, 2);
            $batch_price = round($unit_price * $row['amount'], 2);

//            dump($row);
//            dump($row['uom']);
//            dump($unit_price);
//            dump($grams);
//            dump($batch_price);

            $this->total_converted_cost += $batch_price;
            $this->total_converted_grams += ($grams * $row['amount']);

            $pkg_name = ($new_batch_name ?: $this->name);

//            dump($total_converted_cost);

            if( ! empty($row['ref_number']) && (!empty($row['increment_uid']) && $row['increment_uid'] == 'on')) ///creating metrc packages
            {
                //get start number
                $uid_split = str_split($row['ref_number'],15);

                $xfer_log->save();

                if($row['amount'] < 1) {
                    throw new \Exception("Cannot incremeber UID when amount is less than 1.");
                }

                for($i=1;$i<=$row['amount'];$i++)
                {
                    $uid = $uid_split[0].str_pad( (int)$uid_split[1]++, 9, 0, STR_PAD_LEFT);

                    $create = [
                        'category_id' => $row['category_id'],
                        'brand_id' => $row['brand_id'],
                        'fund_id' => $row['fund_id'],
                        'license_id' => $this->license_id,
                        'tax_rate_id' => $this->tax_rate_id,
                        'name' => $pkg_name,
                        'description' => $this->description,
                        'uom' => $row['uom'],
                        'wt_grams' => config('highline.uom')[$row['uom']],
                        'wt_based' => 1,
                        'parent_id' => $this->id,
                        'batch_number' => ($this->batch_number?:NULL),
                        'status' => 'inventory',
                        'type' => $this->type,
                        'ref_number' => $uid,
                        'units_purchased' => 1,
                        'inventory' => 1,
                        'unit_price' => $unit_price,
                        'unit_tax_amount' => $this->unit_tax_amount,
                        'subtotal_price' => $unit_price,
                        'tax' => 0,
                        'suggested_unit_sale_price' => 0,
                        'min_flex' => 0,
                        'max_flex' => 0,
                        'testing_status' => $this->testing_status,
                        'packaged_date' => ($row['packed_date']?Carbon::parse($row['packed_date']):null),
                        'in_metrc' => 1,
                        'cultivation_date' => $this->cultivation_date,
                    ];

                    $batch = self::create($create);

                    $new_batches_created[] = new TransferLogDetail([
                        'batch_id' => $batch->id,
                        'action' => ($batch->wasRecentlyCreated?'Created':'Updated'),
                        'units' => 1,
                    ]);

                }

                if(! count($new_batches_created)) {
                    throw new \Exception('No Batches Created');
                }

            }
            else
            {

                $pkg = ( ! empty($row['ref_number']) ? $row['ref_number'] : 'PK'.mt_rand(100000, 999999) );

                    $match = [
                        'batch_number' => ($this->batch_number?:null),
                        'category_id' => $row['category_id'],
                        'brand_id' => $row['brand_id'],
                        'fund_id' => $row['fund_id'],
                        'license_id' => $this->license_id,
                        'tax_rate_id' => $this->tax_rate_id,
                        'name' => $pkg_name,
                        'description' => $this->description,
                        'uom' => $row['uom'],
                        'unit_price'=>(string)($unit_price*100),
                        'packaged_date' => ($row['packed_date']?Carbon::parse($row['packed_date']):null)
                    ];

                    if( ! empty($row['ref_number'])) {
                        $match['ref_number'] = $pkg;
                    }

                    $create = [
                        'batch_number' => ($this->batch_number?:null),
                        'category_id' => $row['category_id'],
                        'parent_id' => $this->id,
                        'fund_id' => $row['fund_id'],
                        'license_id' => $this->license_id,
                        'tax_rate_id' => $this->tax_rate_id,
                        'status' => 'Inventory',
                        'type' => $this->type,
                        'ref_number' => $pkg,
                        'inventory' => $row['amount'],
                        'name' => $pkg_name,
                        'description' => $this->description,
                        'uom' => $row['uom'],
                        'units_purchased' => $row['amount'],
                        'unit_price' => $unit_price,
                        'unit_tax_amount' => $this->unit_tax_amount,
                        'subtotal_price' => $batch_price,
                        'cost_includes_cult_tax' => $this->cost_includes_cult_tax,
                        'cultivation_date' => $this->cultivation_date,
                        'packaged_date' => ($row['packed_date']?Carbon::parse($row['packed_date']):null),
//                        'tax' => $this->cult_tax_amount(),
                        'suggested_unit_sale_price' => $this->suggested_unit_sale_price,
                        'min_flex' => $this->min_flex,
                        'max_flex' => $this->max_flex,
                        'in_metrc' => (! empty($row['ref_number']) ? 1 : $this->in_metrc),
                        'testing_status' => $this->testing_status,
                    ];

//dump($match);
//dump($create);
//dd('end');
//                    $batch = $this->firstOrNew($match, $create);
                    $batch = new self($create);
                    $batch->calculateCultTax();

//dd($batch);

                    $batch->save();

                    $xfer_log->save();

                    $xfer_log->transfer_log_details()->create([
                        'batch_id' => $batch->id,
                        'action' => ($batch->wasRecentlyCreated?'Created':'Updated'),
                        'units' => $row['amount'],
                    ]);

            }

        }

//dump($xfer_log);
//        dd($new_batches_created);

        $xfer_log->transfer_log_details()->saveMany($new_batches_created);

//dd('exit');
//        dump($this->total_converted_grams);
//        dump($this->total_converted_cost);

//        $this->total_converted_cost = $total_converted_cost;

//        dump($total_cost);

//        $inventory_loss = $total_cost - $this->total_converted_cost;
        $inventory_loss = (float)bcsub($total_cost, $this->total_converted_cost, 4);
        $inventory_loss_grams = (float)bcsub($total_grams, $this->total_converted_grams, 4);

//        dump('inv loss - cost / grams');
//        dump($inventory_loss);
//        dump($inventory_loss_grams);

        $xfer_log->inventory_loss = round($inventory_loss, 4);
        $xfer_log->inventory_loss_grams = round($inventory_loss_grams, 4);

//        dump('shortage - cost / grams');
//        dump($this->shortage_cost);
//        dump($this->shortage_grams);
//        dd('d');

        $xfer_log->shortage = round($this->shortage_cost, 4);
        $xfer_log->shortage_grams = round($this->shortage_grams, 4);

        $xfer_log->save();

        DB::commit();

        return $batch;
    }

    public function totalInventoryValue()
    {
        return self::select(\DB::raw('SUM(`inventory` * `unit_price`) as total_inventory_value'))->whereNotIn('category_id',[26,27])->first();
    }

    public function derivedInventoryValue()
    {
        //start inventory march
        $startingInventory = 0;
//        $startingInventory = 762247.89;

//        dump($startingInventory);

        //purchases total
        //2019-03-01
        $cost = PurchaseOrder::whereDate('txn_date','>=','2019-03-01')
                ->join('batches', 'orders.id', '=', 'batches.purchase_order_id')
                ->whereNotIn('category_id',[26,27])
                ->sum(\DB::raw('`units_purchased` * `unit_price`'))/100;
//        dump(($cost));

        $vendor_credits = OrderTransaction::wherePaymentMethod('Vendor Credit')->get()->sum('amount');

        //cost of goods
        $sos = SaleOrder::whereDate('txn_date','>=','2019-03-01')->with('order_details_cog')->get();
        $cogs = $sos->sum('cost');
//        dump(($cogs));

        //inventory loss
        $transfer_logs = TransferLog::whereDate('created_at','>=','2019-03-01')->get();
        $inventory_loss = ($transfer_logs->sum('inventory_loss') + $transfer_logs->sum('shortage'));

//        dump(($inventory_loss));

        $inv_value = $startingInventory + $cost - $vendor_credits - $inventory_loss - $cogs;

//        dump(($inv_value));

        return $inv_value;
    }

    public function currentInventory($filters = null, $with = [])
    {
        $builder = self::select(['batches.*', 'brands.name as brand_name', 'categories.name as cat_name'])
            ->join('categories', 'batches.category_id', '=', 'categories.id')
            ->leftjoin('brands', 'batches.brand_id', '=', 'brands.id')
            ->whereNotIn('categories.id',[26,27])
            ->orderBy('categories.name')
            ->orderBy('batches.name')
            ->orderBy('batches.packaged_date');

        if($filters) {
            $builder->filters($filters);
        }

        if(count($with)) {
            $builder->with($with);
        }

//        if(Auth::user()->hasRole('salesrep') && ! Auth::user()->isAdmin()) {
//            $builder->whereNotIn('categories.id', [1,7,20,24,28,29]); //bulk, smalls, shake
//        }

//dump($with);
//        dd($builder);

        return $builder;
    }

    public function isTopParent()
    {
        return ($this->top_level_parent->id == $this->id);
    }

    public function isChild()
    {
        return ( ! $this->isTopParent() );
    }

    public function submitForTesting($sample_size_grams, $ref_number, $package_date, $testing_laboratory_id)
    {
        //source gram cost
        $this_gram_cost = ( $this->uom=='lb' ? $this->unit_price / config('highline.uom.lb') : $this->unit_price );

        $sample_batch = self::create([
            'parent_id'=>$this->id,
            'category_id'=>30,
            'status'=>'Lab',
            'testing_status'=>'Submitted',
            'name'=>$this->name,
            'batch_number'=>$this->batch_number,
            'ref_number'=>$ref_number,
            'units_purchased'=>$sample_size_grams,
            'inventory'=>$sample_size_grams,
            'unit_price'=>$this_gram_cost,
            'subtotal_price'=>($this_gram_cost * $sample_size_grams),
            'tax'=>0,
            'is_medical'=>0,
            'in_metrc'=>1,
            'cultivation_date'=>$this->cultivation_date,
            'packaged_date'=>$package_date,
            'coa_sample'=>1,
        ]);

        $xfer_log = TransferLog::create([
            'user_id' => Auth::user()->id,
            'batch_id' => $this->id,
            'quantity_transferred' => ($this->uom=='lb' ? $sample_size_grams / config('highline.uom.lb') : $sample_size_grams ),
            'start_wt_grams' => $sample_size_grams,
            'packer_name'=>Auth::user()->name,
            'type'=>'Pre-Pack',
            'reason'=>'Lab Test Sample',
        ]);

        $xfer_log->transfer_log_details()->create([
            'batch_id' => $sample_batch->id,
            'action' => 'Created',
            'units' => $sample_size_grams,
        ]);

        $this->testing_laboratory_id = $testing_laboratory_id;
        $this->status = 'Inventory';
        $this->testing_status = 'In-Testing';
        $this->tested_at = Carbon::now();
        $this->coa_batch = 1;

        $this->inventory = $this->inventory - ($this->uom=='lb' ? $sample_size_grams / config('highline.uom.lb') : $sample_size_grams );
        $this->transfer = $this->transfer + ($this->uom=='lb' ? $sample_size_grams / config('highline.uom.lb') : $sample_size_grams );

        $this->save();

        return $sample_batch->refresh();

    }

    public function reconcile($change_to, $current_value=null, $reason, $notes=null)
    {
        if(is_null($current_value)) $current_value = ($this->wt_based ? $this->wt_grams : $this->inventory );
//dump($current_value);
//dump($change_to);
        $loss_qty = bcsub($current_value, $change_to, 4);
        $loss_grams = ($this->wt_based ? $loss_qty : $loss_qty * config('highline.uom')[$this->uom]);

        if($this->wt_based) {
            $loss_cost = ($this->subtotal_price/config('highline.uom')[$this->uom] * $loss_qty);
        } else {
            $loss_cost = ($this->unit_price * $loss_qty);
        }

        $transfer_log_data = [
            'user_id' => Auth::user()->id,
            'batch_id' => $this->id,
            'quantity_transferred' => $loss_qty,
            'inventory_loss' => $loss_cost,
            'inventory_loss_grams' => $loss_grams,
            'packer_name'=>'Reconcile',
            'type'=>'Reconcile',
            'reason'=>$reason,
            'notes'=>$notes,
        ];
//dd($transfer_log_data);
        TransferLog::create($transfer_log_data);

        if($this->wt_based) {

            $this->wt_grams = $change_to;
            $this->unit_price = (float)bcsub($this->unit_price, $loss_cost, 4);
            if($change_to==0) {
                $this->inventory=$change_to;
            }

        } else {
            $this->inventory = $change_to;
        }

        $this->save();

        return $this;
    }

    static public function search($q)
    {
        return self::query()
            ->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    ->orWhere('batch_number', 'like', "%$q%")
                    ->orWhere('ref_number', 'like', "%$q%");
            })
            ->where('inventory','>', 0)
            ->with('category')
            ->with('purchase_order')
            ->with('parent_batch');
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value * 100;
    }

    public function getUnitPriceAttribute($value)
    {
        return $value/100;
    }

    public function setUnitTaxAmountAttribute($value)
    {
        $this->attributes['unit_tax_amount'] = $value * 100;
    }

    public function getUnitTaxAmountAttribute($value)
    {
        if($value) {
            return $value/100;
        } else {
            if(!$this->units_purchased) return 0;
            return ($this->tax/$this->units_purchased);
        }
    }


    public function setTotalInventoryValueAttribute($value)
    {
        $this->attributes['total_inventory_value'] = $value * 100;
    }

    public function getTotalInventoryValueAttribute($value)
    {
        return $value/100;
    }

    public function setSubtotalPriceAttribute($value)
    {
        $this->attributes['subtotal_price'] = $value * 100;
    }

    public function getSubtotalPriceAttribute($value)
    {
        return $value/100;
    }


    public function setSuggestedUnitSalePriceAttribute($value)
    {
        $this->attributes['suggested_unit_sale_price'] = $value * 100;
    }

    public function getSuggestedUnitSalePriceAttribute($value)
    {
        return $value/100;
    }


    public function setMinFlexAttribute($value)
    {
        $this->attributes['min_flex'] = $value * 100;
    }

    public function getMinFlexAttribute($value)
    {
        return $value/100;
    }


    public function setMaxFlexAttribute($value)
    {
        $this->attributes['max_flex'] = $value * 100;
    }

    public function getMaxFlexAttribute($value)
    {
        return $value/100;
    }

    public function setTaxAttribute($value)
    {
        $this->attributes['tax'] = $value * 100;
    }

    public function getTaxAttribute($value)
    {
        return $value/100;
    }

}
