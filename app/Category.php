<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function canTransferTo()
    {
        return (in_array($this->id, [1,4,6,7,11,17,18,19,20]));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->orderBy('name');
    }

}
