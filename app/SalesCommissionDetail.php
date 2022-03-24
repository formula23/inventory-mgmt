<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesCommissionDetail extends Model
{
    protected $fillable = ['sales_rep_id', 'sales_commission_id', 'sale_order_id', 'is_bulk_order', 'rate', 'amount'];

    protected $table = 'sales_commission_details';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sales_rep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sales_commission()
    {
        return $this->belongsTo(SalesCommission::class, 'sales_commission_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_order()
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function getAmountPctAttribute()
    {
        return $this->rate * 100;
    }

    /**
     * @param $value
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getAmountAttribute($value)
    {
        return $value/100;
    }
}
