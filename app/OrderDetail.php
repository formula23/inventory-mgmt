<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $guarded = [];

    public function sale_order()
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function return_order()
    {
        return $this->belongsTo(Order::class, 'return_order_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function order_detail_returned()
    {
        return $this->hasMany(OrderDetail::class, 'parent_id');
    }

    public function parent_order_detail()
    {
        return $this->belongsTo(OrderDetail::class, 'parent_id');
    }

    public function vault_log()
    {
        return $this->hasOne(VaultLog::class);
    }

    public function vault_logs()
    {
        return $this->hasMany(VaultLog::class);
    }

    public function setUnitCostAttribute($value)
    {
        $this->attributes['unit_cost'] = $value * 100;
    }

    public function getUnitCostAttribute($value)
    {
        return $value/100;
    }

    public function setUnitSalePriceAttribute($value)
    {
        $this->attributes['unit_sale_price'] = $value * 100;
    }

    public function getUnitSalePriceAttribute($value)
    {
        return $value/100;
    }

    public function setUnitTaxAmountAttribute($value)
    {
        $this->attributes['unit_tax_amount'] = $value * 100;
    }

    public function getUnitTaxAmountAttribute($value)
    {
        return $value/100;
    }

    public function getCostAttribute()
    {
//        $multiplier = ($this->sale_order->status == 'returned' && $this->cog==1 ? -1 : 1 );
        $multiplier = 1;
        return ($this->unit_cost *  (! is_null($this->units_accepted) ? $this->units_accepted : $this->units) * $multiplier);
    }

    public function getRevenueAttribute()
    {
        if( ! $this->sale_order->hasRevenue() ) return 0;

        $multiplier = ($this->sale_order->status == 'returned' && $this->cog==1 ? -1 : 1 );
        $multiplier = 1;
        return ($this->unit_sale_price * $this->units_accepted) * $multiplier;
    }

    public function getSalePriceAttribute()
    {
        return ($this->unit_sale_price * $this->units);
    }

    public function getLineItemSubtotalAttribute()
    {
//        return ($this->unit_sale_price * abs( ! is_null($this->units_accepted) ? $this->units_accepted : $this->units));
        return ($this->unit_sale_price * ( ! is_null($this->units_accepted) ? $this->units_accepted : $this->units));
    }



    public function getWeightAcceptedGramsAttribute()
    {
        return ($this->units_accepted * config('highline.uom')[$this->batch->uom]) + $this->order_detail_returned->sum('weight_accepted_grams');
    }

    public function getWeightAcceptedPoundsAttribute()
    {
        return ($this->weight_accepted_grams/ config('highline.uom.lb'));
    }

    public function getWeightPendingGramsAttribute()
    {
        return (($this->units - $this->units_accepted) * config('highline.uom')[$this->batch->uom]);
    }

    public function getWeightPendingPoundsAttribute()
    {
        return ($this->weight_pending_grams / config('highline.uom.lb'));
    }

    public function getLineTaxAmountAttribute()
    {
        return ($this->unit_tax_amount * ($this->units_accepted?$this->units_accepted:$this->units));
    }

//
//    public function setSubtotalSalePriceAttribute($value)
//    {
//        $this->attributes['subtotal_sale_price'] = $value * 100;
//    }
//
//    public function getSubtotalSalePriceAttribute($value)
//    {
//        return $value/100;
//    }

    public function getUnitMarginAttribute()
    {
        return ($this->unit_sale_price - $this->unit_cost);
    }

    public function getMarkupPctAttribute()
    {
        if($this->unit_cost==0) return "100%";
        return number_format(($this->unit_margin/$this->unit_cost) * 100, 2)."%";
    }

    public function getMarginActualAttribute()
    {
        $m = $this->unit_margin * ( ! is_null($this->units_accepted) ? $this->units_accepted : 0 );

        return $m + $this->order_detail_returned->sum('margin_actual');
    }

    public function getMarginAttribute()
    {
        return $this->unit_margin * ( ! is_null($this->units_accepted) ? $this->units_accepted : $this->units );
    }

    public function getMarginPctAttribute()
    {
        if(!$this->unit_sale_price) return -100;
        return number_format((($this->unit_margin / $this->unit_sale_price) * 100), 2);
    }

    public function getMarginPctActualAttribute()
    {
        if(!$this->unit_sale_price) return -100;
//        $m = $this->unit_margin * ( ! is_null($this->units_accepted) ? $this->units_accepted : 0 );
        return number_format((($this->unit_margin / $this->unit_sale_price) * 100), 2);
    }

    public function notAccepted()
    {
        return is_null($this->units_accepted);
    }

    public function getSubtotalAttribute()
    {
//        $multiplier = ($this->sale_order->status == 'returned' && $this->cog==1 ? -1 : 1 );
        $multiplier = 1;
        $units = ( ! is_null($this->units_accepted) ? $this->units_accepted : $this->units );
        return ($units * $this->unit_sale_price) * $multiplier;
    }

    public function isCOG()
    {
        return ($this->cog == 1);
    }

}
