<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 7/20/17
 * Time: 17:51
 */

namespace App;

use App\Scopes\ReturnOrderScope;

class ReturnOrder extends Order
{
    protected $table = 'orders';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ReturnOrderScope);
    }

}