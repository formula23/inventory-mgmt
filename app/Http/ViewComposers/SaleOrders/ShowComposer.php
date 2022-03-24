<?php

/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 11/30/17
 * Time: 21:54
 */

namespace App\Http\ViewComposers\SaleOrders;

use Illuminate\View\View;

class ShowComposer
{

    public function compose(View $view)
    {

        $batches_need_retag = $view->saleOrder->batchesThatRequireRetag;

        $warnings = collect();
        if($batches_need_retag->count()) {
            $warnings->push('There are batches that require retagging.');
        }

        $view->with('warnings', $warnings)
            ->with('batches_need_retag', $batches_need_retag);
    }

}