<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TransferLog extends Model
{
    protected $guarded = [];

    protected $dates = ['packed_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function batch_converted()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function batches_converted()
    {
        return $this->hasMany(Batch::class, 'batch_id');
    }

    public function transfer_log_details()
    {
        return $this->hasMany(TransferLogDetail::class);
    }

    public function transfer_log_detail()
    {
        return $this->belongsTo(TransferLogDetail::class);
    }

    public function setInventoryLossAttribute($value)
    {
        $this->attributes['inventory_loss'] = $value * 100;
    }

    public function getInventoryLossAttribute($value)
    {
        return $value/100;
    }

    public function setShortageAttribute($value)
    {
        $this->attributes['shortage'] = $value * 100;
    }

    public function getShortageAttribute($value)
    {
        return $value/100;
    }

    public function getCanUndoAttribute()
    {
        $canundo = true;

        foreach($this->transfer_log_details as $transfer_log_detail)
        {
            $batch_created = $transfer_log_detail->batch_created;

            if( ! $batch_created->inventory || ($batch_created->units_purchased != $batch_created->inventory) ||
                ($batch_created->wt_based && ($batch_created->wt_grams != config('highline.uom')[$batch_created->uom]))) {
                $canundo = false;
                break;
            }
        }

        return $canundo;
    }

    public function scopeInventoryLoss($query)
    {
        return $query
            ->select(
                \DB::raw('DATE_FORMAT(transfer_logs.created_at, "%Y-01-%m") as packed_month_year'),
                'transfer_logs.type',
                \DB::raw('SUM(inventory_loss) as inventory_loss'),
                \DB::raw('SUM(shortage) as shortage'),
                'reason',
                'funds.name as fund_name'
            )
            ->leftJoin('batches', 'transfer_logs.batch_id', '=', 'batches.id')
            ->leftJoin('funds', 'batches.fund_id', '=', 'funds.id')
            ->groupBy(\DB::raw('DATE_FORMAT(transfer_logs.created_at, "%Y-01-%m")'))
            ->groupBy('transfer_logs.type')
            ->groupBy('reason')
            ->groupBy('funds.id')
            ->orderBy('packed_month_year', 'desc');

    }

    public function undo()
    {
        $batch_converted = $this->batch_converted;

        if( $this->quantity_transferred > 0 ||
            ($this->quantity_transferred < 0 && $batch_converted->inventory >= ($this->quantity_transferred * -1)) ) {
            $batch_converted->inventory += $this->quantity_transferred;
            $batch_converted->save();
            $this->delete();
            return true;
        } else {
            return false;
        }
    }

    public function storePackagingLoss(Batch $batch)
    {
        $log = self::create([
            'user_id' => Auth::user()->id,
            'batch_id' => $batch->id,
            'quantity_transferred'=>0,
            'inventory_loss'=>$batch->unit_price,
            'packer_name'=>'System',
            'type'=>'Packaging',
        ]);
    }

}
