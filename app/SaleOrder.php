<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 7/20/17
 * Time: 17:51
 */

namespace App;


use App\Scopes\SaleOrderScope;
use App\Scopes\UserOrderScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleOrder extends Order
{
    protected $table = 'orders';
    protected $payment_type = 'received';
    protected $sales_comm;

//    protected $total_units_sold = [];
    protected $total_grams_sold =[];
    protected $total_lbs_sold = 0;
    protected $units_purchased = [];

    public $latest_order_detail = null;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SaleOrderScope);
        static::addGlobalScope(new UserOrderScope);
    }

    public function sales_commission_details()
    {
        return $this->hasMany(SalesCommissionDetail::class, 'sale_order_id');
    }

    public function return_orders()
    {
        return $this->hasMany(SaleOrder::class, 'parent_id');
    }

    public function parent_order()
    {
        return $this->belongsTo(SaleOrder::class, 'parent_id');
    }

    public function getSubtotalAfterDiscountAttribute($value)
    {
        return ($this->subtotal - $this->discount);
    }

    /**
     * @param $value
     * @return float
     */
    public function getExciseTaxAttribute($value)
    {
        return $value/100;
    }

    public function getCostByFundAttribute()
    {
        return $this->order_details_cog->groupBy(function ($order_detail_cog) {
            return $order_detail_cog->batch->fund->name;
        });
    }

    public function getCostAttribute()
    {
        return $this->order_details_cog->sum('cost');
    }

    public function getRevenueAttribute()
    {
        return $this->order_details_cog->sum('revenue') - $this->discount;
    }

    public function getMarginAttribute()
    {
//        $subtotal = ($this->hasDiscount()? $this->subtotal - $this->discount : $this->subtotal );
//        $subtotal = ($this->hasDiscount()?  : $this->subtotal );
        if( ! $this->hasRevenue()) return 0;
        return ($this->revenue - $this->discount) - $this->cost;
    }

    public function getMarginPctAttribute()
    {
        if( $this->subtotal==0 || $this->margin==0) return 0;
        return number_format((($this->margin / $this->subtotal) * 100), 2);
    }

    public function getBatchesThatRequireRetagAttribute()
    {

        $od_retags = OrderDetail::select('batch_id', DB::raw("count(batch_id) as order_count"))
            ->whereIn('batch_id', $this->order_details->pluck('batch_id'))
            ->groupBy('batch_id')
            ->get()
            ->keyBy('batch_id');

        foreach($this->order_details as $order_detail)
        {
            if(is_null($order_detail->batch)) continue;
            if( $od_retags->get($order_detail->batch->id)->order_count == 1 && $order_detail->batch->inventory == 0) {
                $od_retags->forget($order_detail->batch->id);
            }
        }

        return $od_retags;
    }

    public function hasRevenue()
    {
        return (in_array($this->sale_type, ['co-pack','promotional','transfer']) ? false : true );
    }

    public function addUpdateItem($batch, $sold_as_name, $quantity, $sale_price)
    {

        if(!$quantity) {
            throw new \Exception("Quantity required!");
        }

        if($batch) {

            if($order_detail = $this->getOrderDetail($batch->id, $sale_price, $sold_as_name)) {
                $order_detail->units = bcadd($order_detail->units, $quantity, 2);
//            $order_detail->subtotal_sale_price = $order_detail->units * $sale_price;
                $order_detail->save();
            } else {
                $order_detail = new OrderDetail();
                $order_detail->batch_id = $batch->id;
                $order_detail->sold_as_name = $sold_as_name;
                $order_detail->units = $quantity;
                $order_detail->unit_cost = $batch->unit_price;
                $order_detail->unit_sale_price = $sale_price;
                $order_detail->unit_tax_amount = (request()->get('pass_cult_tax') ? $batch->unit_tax_amount : null);
//            $order_detail->subtotal_sale_price = $quantity * $sale_price;

                $this->order_details()->save($order_detail);

            }

            $batch->inventory = bcsub($batch->inventory, $quantity, 4);
            $batch->save();

        } else {

            $order_detail = new OrderDetail();
            $order_detail->sold_as_name = $sold_as_name;
            $order_detail->units = $quantity;
            $order_detail->unit_cost = 0;
            $order_detail->unit_sale_price = $sale_price;
            $order_detail->cog = 0;

            //            $order_detail->subtotal_sale_price = $quantity * $sale_price;

            $this->order_details()->save($order_detail);

        }

        $this->latest_order_detail = $order_detail;

        return $this;

    }

    public function calculateTotals()
    {
        $this->refresh();

        $this->subtotal = $this->order_details->sum('subtotal') - $this->order_details->sum('line_tax_amount');

        $taxable_subtotal = ($this->excise_tax_pre_discount ? $this->subtotal : $this->subtotal - $this->discount );
        $this->tax = ($this->hasExciseTax() ? $taxable_subtotal * config('highline.excise_tax_rate') : 0 );

//        $this->transpo_tax = (in_array($this->customer_type, ['retailer','distributor']) ? $this->subtotal * config('highline.transpo_tax_rate') : 0 );

        $this->total = ($this->subtotal + $this->tax + $this->transpo_tax) - $this->discount;

//        $this->total -= $this->discount;
//        dump($this->discount);
//        dd($this);

        $this->balance = $this->total - $this->transactions->sum('amount');

        $this->save();

        return $this;
    }

    public function removeItem(OrderDetail $orderDetail)
    {
        $batch = $orderDetail->batch;

        try {

            DB::beginTransaction();

            $orderDetail->vault_logs()->update(["order_detail_id"=>null]);

            if($orderDetail->cog) {
                $batch->inventory += ( ! is_null($orderDetail->units_accepted) ? $orderDetail->units_accepted : $orderDetail->units );
                $batch->save();
            }

            $orderDetail->delete();

            DB::commit();

            return $orderDetail;

        } catch(\Exception $e) {

            DB::rollBack();
            return $e;
        }

    }

    public function tax_passed_on()
    {

        $tax_amounts_passed_on = [];

        if(!$this->order_details) return $tax_amounts_passed_on;

        $this->order_details->each(function($order_detail) use (&$tax_amounts_passed_on) {
            if(!$order_detail->unit_tax_amount) return;

            @$tax_amounts_passed_on[$order_detail->batch->tax_rate->name][$order_detail->batch->uom]['weight'] += $order_detail->{($order_detail->units_accepted?"units_accepted":"units")};
            @$tax_amounts_passed_on[$order_detail->batch->tax_rate->name][$order_detail->batch->uom]['line_tax_rate'] = $order_detail->unit_tax_amount;
            @$tax_amounts_passed_on[$order_detail->batch->tax_rate->name][$order_detail->batch->uom]['total_line_tax_amount'] += ($order_detail->{($order_detail->units_accepted?"units_accepted":"units")} * $order_detail->unit_tax_amount);

        });

        return $tax_amounts_passed_on;
    }

    public function getTotalTaxPassedOnAttribute()
    {
        $total_tax=0;
        $this->order_details->each(function($order_detail) use (&$total_tax) {
            $total_tax += ($order_detail->{($order_detail->units_accepted?"units_accepted":"units")} * $order_detail->unit_tax_amount);
        });
        return $total_tax;
    }

    public function open()
    {
        $this->status = 'open';
        $this->save();
        return $this;
    }

    public function in_transit()
    {
        $this->status = 'in-transit';
        $this->save();
        return $this;
    }

    public function ready_for_delivery()
    {
        $this->status = 'ready for delivery';
        $this->save();
        return $this;
    }

    public function delivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = Carbon::now();

        //update due date with terms
        $this->setDueDate();
//dd($this);
        $this->save();
        return $this;
    }

    /**
     * @return $this
     */
    public function close()
    {
        $this->status = 'closed';
        $this->save();
        return $this;
    }

    public function setDueDate()
    {
        if($this->expected_delivery_date || $this->delivered_at) {
            $this->due_date = ($this->expected_delivery_date ? $this->expected_delivery_date->addDays($this->terms) : $this->delivered_at->addDays($this->terms));
        }
    }

    public function getOrderDetail($batch_id, $sale_price, $sold_as_name)
    {
        return $this->order_details()
            ->where('batch_id', $batch_id)
            ->where('sold_as_name', $sold_as_name)
            ->get()
            ->where('unit_sale_price', $sale_price)
            ->first();
    }

    public function hasExciseTax()
    {
        if($this->destination_license) {
            return ( ! empty($this->destination_license) && in_array($this->destination_license->license_type_id, [4,5,11]));
        } else {
            return (bool)(stripos($this->customer_type, 'retailer')!==false);
        }
    }

    public function hasOrderDetailWithNoPrice()
    {
        $needs_price = false;
        $this->order_details->each(function($order_detail) use (&$needs_price) {
            if(is_null($order_detail->unit_sale_price) || $order_detail->unit_sale_price===0) {
                $needs_price = true;
            }
        });

        return $needs_price;
    }

    public function hasDiscount()
    {
        return (bool)($this->discount > 0);
    }

    public function isOpen()
    {
        return (bool)($this->status =='open');
    }

    public function isReadyForDelivery()
    {
        return (bool)($this->status == 'ready for delivery');
    }

    public function isInTransit()
    {
        return (bool)($this->status == 'in-transit');
    }

    public function isDelivered()
    {
        return (bool)($this->status == 'delivered');
    }

    public function todaysOrders()
    {
        return static::select(DB::raw("COUNT(id) as order_count"), DB::raw("sum(subtotal) as subtotal"))
            ->whereDate('txn_date', Carbon::now()->toDateString())
            ->whereIn('status',['delivered','returned'])
            ->first();
    }

    public function weeksOrders()
    {
        return static::select(DB::raw("COUNT(id) as order_count"), DB::raw("sum(subtotal) as subtotal"))
            ->whereBetween('txn_date', [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
            ->whereYear('txn_date', Carbon::now()->year)
            ->whereIn('status',['delivered','returned'])
            ->first();
    }

    public function monthsOrders()
    {
        return static::select(DB::raw("COUNT(id) as order_count"), DB::raw("sum(subtotal) as subtotal"))
            ->whereMonth('txn_date', Carbon::now()->month)
            ->whereYear('txn_date', Carbon::now()->year)
            ->whereIn('status',['delivered','returned'])
            ->first();
    }

    public function quartersOrders()
    {
        return static::select(DB::raw("COUNT(id) as order_count"), DB::raw("sum(subtotal) as subtotal"))
            ->where(DB::raw("QUARTER(txn_date)"), DB::raw("QUARTER(NOW())"))
            ->whereYear('txn_date', Carbon::now()->year)
            ->whereIn('status',['delivered','returned'])
            ->first();
    }

    public function exciseTax()
    {
        return static::select(DB::raw("COUNT(id) as order_count"), DB::raw("sum(tax) as excise_tax"), DB::raw('QUARTER(txn_date) as Quarter'), DB::raw('YEAR(txn_date) as Year'))
//            ->where(DB::raw("QUARTER(txn_date)"), '>=', DB::raw("QUARTER(date_sub(NOW(), INTERVAL 1 QUARTER))"))
//            ->whereYear('txn_date', Carbon::now()->year)
            ->whereDate('txn_date','>=',Carbon::now()->subQuarter(1)->firstOfQuarter())
            ->whereIn('status',['delivered','returned'])
            ->groupBy('Quarter')
            ->groupBy('Year')
            ->get();
    }

    public function set_order_id()
    {
        $this->ref_number = $this->new_ref_number(($this->sale_type=='transfer'?'TR':'SO'));
        $this->save();
        return $this;
    }

    static public function search($q)
    {
        parent::$search_table = 'customer';
        return parent::search($q);
    }


}