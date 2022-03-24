<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 7/13/17
 * Time: 12:46
 */

namespace App\Repositories;

use App\Batch;
use App\Category;
use App\Conversion;
use App\License;
use App\Log;
use App\TaxRate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\Order;
use App\PurchaseOrder;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;

class DbPurchaseOrderRepository extends DbOrderRepository implements PurchaseOrderRepositoryInterface
{
    protected $order_class = PurchaseOrder::class;
    protected $order_type = 'purchase';
    protected $selected_category = null;
    protected $data;

    public function create($data)
    {
        $this->data = $data;
        $this->data['user_id'] = Auth::user()->id;
        $this->data['type'] = $this->order_type;
        $this->data['ref_number'] = null;
        $this->data['transpo_tax']=0;
        $this->data['due_date'] = Carbon::parse($this->data['txn_date'])->addDays($this->data['terms']);

        $license = License::with('license_type')->where('id', $this->data['origin_license_id'])->first();
        $this->data['customer_type'] = $license->license_type->name;

        $this->data['subtotal'] = 0;
        $this->data['tax'] = 0;
        $this->data['total'] = 0;
        $this->data['balance'] = 0;

        $purchase_order = app($this->order_class)->create($this->data);
        $purchase_order->set_order_id();

        $selected_category=null;

        $batch=[];
        for($i=0; $i < count($this->data['_batches']['batch_number']); $i++)
        {
            foreach($this->data['_batches'] as $field=>$vals)
            {
                $batch[$field] = $vals[$i];
            }
            $purchase_order->addBatch($batch);
        }

        $purchase_order->refresh();
        $purchase_order->updateTotals();

        return $purchase_order;

    }

    private function empty_batch($batch)
    {
        return is_null($batch['quantity']);
    }

    private function set_selected_category($cat_id)
    {
        if( ! $this->selected_category or $this->selected_category->id != $cat_id)
            $this->selected_category = Category::find($cat_id);
        return;
    }

}