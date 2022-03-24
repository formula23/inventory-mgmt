<?php

namespace App;

use App\Presenters\PresentableTrait;
use Illuminate\Database\Eloquent\Model;

class VaultLog extends Model
{
    use PresentableTrait;

    protected $presenter = \App\Presenters\VaultLog::class;

    protected $guarded = [];

    protected $table = 'vault_logs';

    public function scopeFilters($query, $filters)
    {
        return $filters->apply($query);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function broker()
    {
        return $this->belongsTo(User::class, 'broker_id');
    }

    public function order_detail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    /**
     * @param $value
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }

    /**
     * @param $value
     * @return float
     */
    public function getPriceAttribute($value)
    {
        return $value/100;
    }

    public function getCanBeAddedToSaleOrderAttribute()
    {
        return ($this->quantity < 0 &&
            is_null($this->order_detail_id) &&
            $this->quantity * -1 < $this->batch->inventory);

    }

    public static function currentSessionLogs($session_id = null)
    {
        if(is_null($session_id)) $session_id = session()->getId();

        return self::whereSessionId($session_id)
            ->with('batch');
    }



}

