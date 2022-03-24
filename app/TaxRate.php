<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaxRate extends Model
{
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    static public function cultivation_tax_rates()
    {
        return self::select('id', DB::raw('CONCAT(name," ($",amount,"/",uom,")") As name'))->where('category','Cultivation')->where('is_active', 1)->pluck('name','id');
    }

}
