<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 6/28/17
 * Time: 01:39
 */

namespace App\Repositories;

use Illuminate\Support\ServiceProvider;


class BackendServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'App\Repositories\Contracts\UserRepositoryInterface',
            'App\Repositories\DbUserRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\OrderRepositoryInterface',
            'App\Repositories\DbOrderRepository'
        );
        
        $this->app->bind(
            'App\Repositories\Contracts\BasketRepositoryInterface',
            'App\Repositories\DbBasketRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\PurchaseOrderRepositoryInterface',
            'App\Repositories\DbPurchaseOrderRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\SaleOrderRepositoryInterface',
            'App\Repositories\DbSaleOrderRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\ProductRepositoryInterface',
            'App\Repositories\DbProductRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\BatchRepositoryInterface',
            'App\Repositories\DbBatchRepository'
        );
    }

}