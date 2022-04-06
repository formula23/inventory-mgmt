<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 7/20/17
 * Time: 17:50
 */

namespace App\Repositories;

use App\License;
use App\LicenseType;
use App\Repositories\Contracts\SaleOrderRepositoryInterface;
use App\SaleOrder;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DbSaleOrderRepository extends DbOrderRepository implements SaleOrderRepositoryInterface
{
    protected $order_class = SaleOrder::class;
    protected $order_type = 'sale';


    public function create($data)
    {
//        dd($data);
        $user = Auth::user();

        $data['customer_id'] = $data['customer']->id;
//        $data['bill_to_id'] = $data['bill_to_id'];
        $data['txn_date'] = ($data['txn_date']?:Carbon::now()->format('Y-m-d'));
        $data['expected_delivery_date'] = ($data['expected_delivery_date']?:null);
        $data['user_id'] = $user->id;
//        $data['sales_rep_id'] = $data['sales_rep_id'];
//        $data['broker_id'] = $data['broker_id'];
        $data['type'] = $this->order_type;
        $data['excise_tax_pre_discount'] = 0;
        $data['ref_number'] = $this->data['ref_number'] = null;
        $data['subtotal']=0;
        $data['tax']=0;
        $data['transpo_tax']=0;
        $data['total']=0;
        $data['balance']=0;
//dd($data);
//        $license = License::find($data['destination_license_id']);

        $st_date = ( ! is_null($data['expected_delivery_date']) ? Carbon::parse($data['expected_delivery_date']) : Carbon::parse($data['txn_date']));

        $data['due_date'] = $st_date->addDays($data['terms']);

        unset($data['customer']);

        $sale_order = app($this->order_class)->create($data);

        $sale_order->set_order_id();

        return $sale_order;
    }

    public function salesByBatchId($batch_id)
    {
        $q = SaleOrder::with('order_details.batch','customer')
            ->with([
                'order_details'=>function($q) use ($batch_id) {
                $q->where('batch_id', $batch_id);
            },
                'broker'])
            ->whereHas('order_details', function ($query) use ($batch_id) {
                $query->where('batch_id', $batch_id);
            })
            ->orderBy('txn_date', 'desc')
            ->orderBy('ref_number', 'desc')->get();

        return $q;

    }
}