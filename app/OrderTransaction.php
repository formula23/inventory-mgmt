<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    protected $guarded = [];

    protected $dates = ['txn_date'];

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute($value)
    {
        return $value/100;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function sale_order()
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function return_order()
    {
        return $this->belongsTo(SaleOrder::class, 'return_order_id');
    }

    public function txn_date()
    {
        $field = ( ! $this->txn_date ? 'created_at' : 'txn_date' );
        return $this->{$field}->format('m/d/Y');
    }



}
