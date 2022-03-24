<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function purchase_orders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

}
