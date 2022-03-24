<?php

namespace App;

use App\Events\ProductCreated;
use App\Repositories\DbSaleOrderRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    protected $guarded = [];

    protected $table = 'products';

    protected $dates = ['inventory_at', 'transit_at', 'sold_at', 'returned_at'];

    protected $events = [
        'created' => ProductCreated::class,
    ];

    public function setUnitSalePriceAttribute($value)
    {
        $this->attributes['unit_sale_price'] = (int)$value * 100;
    }

    public function getUnitSalePriceAttribute($value)
    {
        return $value/100;
    }

//    public function setSubtotalSalePriceAttribute($value)
//    {
//        $this->attributes['subtotal_sale_price'] = (int)$value * 100;
//    }
//
//    public function getSubtotalSalePriceAttribute($value)
//    {
//        return $value/100;
//    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_order()
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function transporter()
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }

    public function activity_logs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function activity_logs_with_user()
    {
        return $this->activity_logs()
            ->orderby('created_at', 'desc')
            ->with('user')
            ->get();
    }

    public function sell(SaleOrder $saleOrder)
    {
        $this->status = 'sold';
        $this->sold_as_name = (request('sold_as_name') != $this->batch->name ? request('sold_as_name') : null );
        $this->sold_at = Carbon::now();
        $this->unit_sale_price = request('sale_price');
//        $this->subtotal_sale_price = request('sale_price');

        $this->sale_order_id = $saleOrder->id;
        $this->save();

        $this->track_action('Sold')->save();

        $this->load(['sale_order']);

        return $this;
    }

    /**
     * @return $this
     */
    public function pickup()
    {
        $this->transporter_id = Auth::user()->id;
        $this->transit_at = Carbon::now();
        $this->status = 'transit';

        $this->track_action('Picked Up')->save();

        return $this;
    }

    public function returned()
    {
        $this->status = 'inventory';
        $this->transporter_id = null;
        $this->returned_at = Carbon::now();
        $this->track_action('Returned')->save();
        return $this;
    }

    public function approve_return()
    {
        $this->status = 'inventory';
        $this->transporter_id = null;
        $this->track_action('Returned Approved')->save();

        return $this;
    }

    public function track_action($action)
    {
        $this->activity_logs()->save(ActivityLog::create([
            'user_id'=>auth()->user()->id,
            'action'=>$action,
        ]));
        return $this;
    }

    public function remove_from_sale_order()
    {
        $this->sale_order_id = null;
        $this->sold_as_name = null;
        $this->status = 'transit';
        $this->unit_sale_price = null;
//        $this->subtotal_sale_price = null;
        $this->save();

        $this->track_action('Remove from sale order');

        return $this;
    }
}
