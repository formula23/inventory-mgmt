<?php

/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 11/30/17
 * Time: 21:54
 */

namespace App\Http\ViewComposers\Home;

use Illuminate\Support\Collection;
use Illuminate\View\View;

class IndexComposer
{

    public function compose(View $view)
    {

        //group all customers collection

        $customers_by_days = [];
        $view->customers->map(function ($item) use (&$customers_by_days) {

            switch(true) {
                case $item->days_last_order >= 60:
                    $customers_by_days['60']['label'] = "More than 60 days";
                    $customers_by_days['60']['customers'][] = $item;
                    break;
                case $item->days_last_order >= 30:
                    $customers_by_days['30']['label'] = "30 - 60 Days";
                    $customers_by_days['30']['customers'][] = $item;
                    break;
                case $item->days_last_order >= 15:
                    $customers_by_days['15']['label'] = "15 - 30 days";
                    $customers_by_days['15']['customers'][] = $item;
                    break;
                default:
                    $customers_by_days['0']['label'] = "Less than 15";
                    $customers_by_days['0']['customers'][] = $item;
                    break;
            }
        });

        $excise_tax_by_quarter = $view->excise_tax->groupBy('Quarter');

        $view->with(compact('excise_tax_by_quarter', 'customers_by_days'));

    }

}