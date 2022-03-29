<?php

namespace App;

use App\Scopes\UserOrderScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $guarded = [];

    protected $table = 'orders';
    protected static $search_table = null;
    protected $dates = ['txn_date','due_date','expected_delivery_date','delivered_at'];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bill_to()
    {
        return $this->belongsTo(User::class, 'bill_to_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sales_rep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function broker()
    {
        return $this->belongsTo(User::class, 'broker_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function order_details_cog()
    {
        return $this->hasMany(OrderDetail::class)->where('cog', 1)->with('sale_order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(OrderTransaction::class);
    }

    public function origin_license()
    {
        return $this->belongsTo(License::class, 'origin_license_id');
    }

    public function destination_license()
    {
        return $this->belongsTo(User::class, 'destination_license_id');
    }

    /**
     * @param $query
     * @param $filters
     * @return mixed
     */
    public function scopeFilters($query, $filters)
    {
        return $filters->apply($query);
    }

    public function scopeOpenOrders($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * @param $query
     * @return mixed
     */
//    public function scopePurchases($query)
//    {
//        return $query->where('type', 'purchase');
//    }

    /**
     * @param $query
     * @return mixed
     */
//    public function scopeSold($query)
//    {
//        return $query->where('type', 'sold');
//    }

    /**
     * @param $query
     * @return mixed
     */
//    public function scopeReturns($query)
//    {
//        return $query->where('type', 'return');
//    }

    /**
     * @param $value
     */
    public function setSubtotalAttribute($value)
    {
        $this->attributes['subtotal'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getSubtotalAttribute($value)
    {
        return $value/100;
    }

    /**
     * @param $value
     */
    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getDiscountAttribute($value)
    {
        return $value / 100;
    }

    /**
     * @param $value
     */
    public function setTaxAttribute($value)
    {
        $this->attributes['tax'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getTaxAttribute($value)
    {
        return $value/100;
    }

    /**
     * @param $value
     */
    public function setTranspoTaxAttribute($value)
    {
        $this->attributes['transpo_tax'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getTranspoTaxAttribute($value)
    {
        return $value/100;
    }

    /**
     * @param $value
     */
    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getTotalAttribute($value)
    {
        return $value/100;
    }

    /**
     * @param $value
     */
    public function setBalanceAttribute($value)
    {
        $this->attributes['balance'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getBalanceAttribute($value)
    {
        return $value/100;
    }

    public function getDestinationLicenseNameLicAttribute()
    {

        if($this->destination_license()->exists()) {
            return ($this->destination_license->user->id != $this->customer_id ? $this->destination_license->user->name." " : "") . $this->destination_license->number." - ".$this->destination_license->license_type->name;
        } else {
            return ucfirst($this->customer_type);
        }
    }

    /**
     * @param $amount
     * @param $txn_date
     * @return $this
     */
    public function applyPayment($amount, $txn_date, $payment_method=null, $ref_number=null, $memo=null)
    {
        $txn = new OrderTransaction();
        $txn->user_id = (Auth::user()?Auth::user()->id:13);
        $txn->amount = $amount;
        $txn->type = $this->payment_type;
        $txn->txn_date = $txn_date;
        $txn->payment_method = $payment_method;
        $txn->ref_number = $ref_number;
        $txn->memo = $memo;

        $this->balance = bcsub($this->balance, $txn->amount, 2);

        $this->transactions()->save($txn);

        if($this instanceof PurchaseOrder && $this->balance == 0) {
            $this->status = 'closed';
        }

        $this->save();

        return $this;
    }

    public function scopeWithOutstandingBalance($query)
    {
        return $query->where('balance','!=',0)->orderBy('txn_date');
    }

    public function scopeWithPastDueBalance($query)
    {
        return $query->where('balance','!=',0)
            ->whereDate('due_date','<',Carbon::today())
            ->orderBy('due_date');
    }

    public function new_ref_number($order_type)
    {
        return $order_type.Carbon::now()->format('ny').'-'.sprintf('%06d', $this->id);
    }

    static public function customer_type()
    {
        return static::select('customer_type')
            ->whereNotNull('customer_type')
            ->groupBy('customer_type');
    }

    static public function search($q)
    {
        return self::query()
            ->select('orders.*')
            ->join('users', 'orders.'.self::$search_table.'_id', '=','users.id')
            ->where(function($qry) use ($q) {
                $qry->where('users.name', 'like', '%'.$q.'%')
                    ->orWhere('ref_number', 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.business_name\')) AS CHAR)'), 'like', '%'.$q.'%');

            })
            ->with(self::$search_table)
            ->orderBy('txn_date');
//

    }

}
