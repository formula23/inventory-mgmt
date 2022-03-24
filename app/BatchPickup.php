<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchPickup extends Model
{
    protected $guarded = [];

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
    public function transporter()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @param $quantity
     * @return $this
     * @throws \Exception
     */
    public function sell($quantity)
    {
        return $this->release($quantity);
    }
    
    public function release($quantity)
    {
        $this->units = bcsub($this->units, $quantity, 2);
        if((float)$this->units === 0.0) {
            $this->delete();
        } else {
            $this->save();
        }
        return $this;
    }
    
}
