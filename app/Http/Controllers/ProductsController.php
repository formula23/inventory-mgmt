<?php

namespace App\Http\Controllers;

use App\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SaleOrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\DbProductRepository;
use App\Repositories\DbSaleOrderRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProductsController extends Controller
{

    public function index(ProductRepositoryInterface $productRepositoryInterface)
    {

        if(Gate::denies('products.index')) {
            return redirect(route('dashboard'));
        }

        $with=[
            'batch',
            'batch.purchase_order',
            'batch.purchase_order.vendor',
            'sale_order',
            'sale_order.customer',
        ];
        $filters = request('filters');
        $products = $productRepositoryInterface->all($with, $filters);
        
        return view('products.index', compact('products', 'filters'));
    }

    public function show(Product $product,
                         UserRepositoryInterface $userRepositoryInterface, 
                         SaleOrderRepositoryInterface $saleOrderRepositoryInterface)
    {
//dd($product);
        if(Gate::denies('products.show') ||
            Gate::denies('products.show.'.$product->status) ||
            Gate::denies('products.show.sellreturn', $product)
        ) {
            flash()->success('Access denied');
            return redirect(route('dashboard'));
        }

        $open_sales_orders = $saleOrderRepositoryInterface->getOpenSaleOrders();

        $customers = $userRepositoryInterface->customers()->get();
        
        return view('products.show', compact('product', 'customers', 'open_sales_orders'));
    }

    public function pickup(Request $request, Product $product)
    {
        if($product->status=='transit') {
            flash()->info($product->batch->name.' was already picked up!');
            return back();
        }
        $product->pickup();

        flash()->success($product->batch->name.' picked up!');

        return redirect(route('products.pickup-success', ['id'=>$product->ref_number]));
    }

    public function approveReturn(Request $request, Product $product)
    {
        $product->approve_return();
        
        return back();
    }

    public function pickupSuccess()
    {
        return view('products.success');
    }

    public function sellReturn(Request $request, Product $product, SaleOrderRepositoryInterface $saleOrderRepositoryInterface)
    {

        if($product->status == 'sold') { //can't sell again
            return back();
        }

        if($request->get('action') == 'sell') {


            $saleOrder = new DbSaleOrderRepository();

            if(request('customer_id')) {
                $sale_order = $saleOrder->create(request('customer_id'));
            }
            elseif(request('sale_order_id')) {
                $sale_order = $saleOrder->find(request('sale_order_id'));
            }
            
            $product = $product->sell($sale_order);
            
            flash()->success($product->batch->name.' added to sale order');
            
            return redirect(route('sale-orders.show', $product->sale_order->id));

        } elseif($request->get('action') == 'return') {
            
            $product->returned();

            flash()->success($product->batch->name.' returned');
            
            return redirect(route('dashboard'));

        }


    }

    public function activity(Product $product)
    {
        $activity_logs = $product->activity_logs_with_user();

        return view('products.activity', compact('activity_logs', 'product'));
    }

}
