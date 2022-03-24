<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 2/19/19
 * Time: 08:57
 */

namespace App;

use App\Scopes\VendorScope;
use App\User;

class Vendor extends User
{
    protected $table = 'users';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new VendorScope);
    }

    public function licenses()
    {
        return $this->hasMany(License::class, 'user_id');
    }

    public static function all($columns = ['*'], $active = 1)
    {
//        parent::all();

        return (new static)
            ->newQuery()
            ->where('active', $active)
            ->orderBy('name')
            ->get(
            is_array($columns) ? $columns : func_get_args()
        );

//        return static::where('active',1)->get();
    }

    public function scopeWithBalance($query)
    {
        return $query->select('users.*',\DB::raw('sum(orders.balance) as outstanding_balance'))
            ->leftjoin('orders', 'users.id', '=', 'orders.vendor_id')
            ->with(['purchase_orders' => function ($query) {
                $query->where('balance','!=',0)
                    ->with('transactions')
                    ->orderBy('txn_date', 'desc')
                    ->orderBy('ref_number', 'desc');
            }])
            ->where('orders.balance','!=',0)
            ->groupBy('users.id')
            ->orderBy('outstanding_balance', 'desc');
    }

}