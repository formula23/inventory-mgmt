<?php

namespace App\Providers;

use App\Batch;
use App\OrderDetail;
use App\PurchaseOrder;
use App\Repositories\DbBasketRepository;
use App\Repositories\DbBatchRepository;
use App\Repositories\DbOrderRepository;
use App\Repositories\DbProductRepository;
use App\Repositories\DbPurchaseOrderRepository;
use App\Repositories\DbSaleOrderRepository;
use App\Repositories\DbUserRepository;
use App\TransferLog;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     */
    public function boot()
    {

        parent::boot();

        Route::bind('user', function($id) {
            return (new DbUserRepository())->find($id);
        });

        Route::bind('basket', function($id) {
            return (new DbBasketRepository)->find($id);
        });

        Route::bind('product', function($id) {
            return (new DbProductRepository)->findByRefNumber($id, ['batch','batch.category','batch.purchase_order','sale_order','transporter']);
        });

        Route::bind('purchase_order', function($id) {
            return PurchaseOrder::where('id', $id)->first();
//            return (new DbPurchaseOrderRepository())->find($id, ['batches.child_batches', 'vendor', 'customer']);
        });

        Route::bind('sale_order', function($id) {
            return (new DbSaleOrderRepository())->find($id, ['vendor', 'customer']);
        });

        Route::bind('batch', function($id) {
            return (new DbBatchRepository())->findByRefNumber($id);
        });

        Route::bind('order_detail', function($id) {
            return OrderDetail::find($id);
        });

        Route::bind('transfer_log', function($id) {
            return TransferLog::find($id);
        });

    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
