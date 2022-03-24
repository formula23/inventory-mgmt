<?php

namespace App\Events;

/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 8/4/17
 * Time: 13:00
 */
class ProductCreated
{
    public function __construct($model)
    {
        $model->track_action('Added to Inventory');
        return true;
    }

}