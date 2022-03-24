<?php

namespace App\Providers;

use App\Http\ViewComposers\Accounting\ReceivablesAgingComposer;
use App\Http\ViewComposers\Accounting\ReceivablesComposer;
use App\Http\ViewComposers\Home\IndexComposer;
use App\Http\ViewComposers\PurchaseOrders\ReTagComposer;
use App\Http\ViewComposers\PurchaseOrders\ReviewUploadComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Http\ViewComposers\Batches\IndexComposer as BatchIndexComposer;
use App\Http\ViewComposers\Batches\ShowComposer as BatchShowComposer;
use App\Http\ViewComposers\Batches\TransferLogComposer as BatchTransferLogComposer;
use App\Http\ViewComposers\PurchaseOrders\IndexComposer as POIndexComposer;
use App\Http\ViewComposers\PurchaseOrders\ShowComposer as POShowComposer;
use App\Http\ViewComposers\SaleOrders\IndexComposer as SOIndexComposer;
use App\Http\ViewComposers\SaleOrders\ShowComposer as SOShowComposer;
use App\Http\ViewComposers\SaleOrders\InvoiceComposer as SOInvoiceComposer;
use App\Http\ViewComposers\SaleOrders\ShippingManifestComposer as SOShipManifestComposer;
use App\Http\ViewComposers\SaleOrders\RetagUidsComposer as SORetagUidComposer;
use App\Http\ViewComposers\Accounting\SalesRepCommissionsComposer;


class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('index', IndexComposer::class);
        View::composer('batches.index', BatchIndexComposer::class);
        View::composer('batches.show', BatchShowComposer::class);
        View::composer('batches.transfer-log', BatchTransferLogComposer::class);

        View::composer('purchase_orders.index', POIndexComposer::class);
        View::composer('purchase_orders.show', POShowComposer::class);
        View::composer('purchase_orders.review-upload', ReviewUploadComposer::class);
        View::composer('purchase_orders.retag', ReTagComposer::class);

        View::composer('sale_orders.index', SOIndexComposer::class);
        View::composer('sale_orders.show', SOShowComposer::class);
        View::composer('sale_orders.invoice', SOInvoiceComposer::class);
        View::composer('sale_orders.retag_uids', SORetagUidComposer::class);

//        View::composer('accounting.sales_rep_commissions', SOIndexComposer::class);
        View::composer('accounting.sales_rep_commissions', SalesRepCommissionsComposer::class);

        View::composer('sale_orders.shipping-manifest', SOShipManifestComposer::class);
        View::composer('batches.print-inventory', BatchIndexComposer::class);
        View::composer('accounting.receivables_aging', ReceivablesAgingComposer::class);

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
