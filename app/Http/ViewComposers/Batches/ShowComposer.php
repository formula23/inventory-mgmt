<?php

/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 11/30/17
 * Time: 21:54
 */

namespace App\Http\ViewComposers\Batches;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShowComposer
{

    public function compose(View $view)
    {

        $view->batch->load('order_details', 'category','purchase_order', 'parent_batch', 'child_batches');

        $view->sale_price = '';

        if($view->batch->suggested_unit_sale_price) {
            $view->sale_price = $view->batch->suggested_unit_sale_price;
        }
        elseif( ! empty(config('highline.sell_price')[$view->batch->brand_id][$view->batch->uom])) {
            $view->sale_price = (config('highline.sell_price')[$view->batch->brand_id][$view->batch->uom]['SoCal'] * ($view->batch->wt_grams/config('highline.uom')[$view->batch->uom]));
        }

//        foreach($view->open_sales_orders as &$open_sales_order) {
//
//            if($open_sales_order->destination_license()->exists()) {
//                $open_sales_order->destination_license_display = $open_sales_order->destination_license->number." - ".$open_sales_order->destination_license->license_type->name;
//            } else {
//                $open_sales_order->destination_license_display = ucfirst($open_sales_order->customer_type);
//            }
//        }

    }

}