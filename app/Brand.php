<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = [];


    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

}
