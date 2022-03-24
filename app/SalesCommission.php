<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesCommission extends Model
{
    protected $guarded = [];

    protected $table = 'sales_commissions';

    protected $dates = ['period_start','period_end'];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sales_rep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function sales_commission_details()
    {
        return $this->hasMany(SalesCommissionDetail::class);
    }

    public function getPeriodStartFormattedAttribute()
    {
        return $this->period_start->format('m/d/Y');
    }

    public function getPeriodEndFormattedAttribute()
    {
        return $this->period_end->format('m/d/Y');
    }

    /**
     * @param $value
     */
    public function setTotalRevenueAttribute($value)
    {
        $this->attributes['total_revenue'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getTotalRevenueAttribute($value)
    {
        return $value/100;
    }

    /**
     * @param $value
     */
    public function setTotalCommissionAttribute($value)
    {
        $this->attributes['total_commission'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getTotalCommissionAttribute($value)
    {
        return $value/100;
    }
}

