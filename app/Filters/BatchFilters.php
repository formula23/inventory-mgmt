<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 11/30/17
 * Time: 16:56
 */

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BatchFilters extends Filters
{
    protected $default_filters = ['in_stock'=>['Yes'=>'Yes']];
    protected $filters = ['status','name','category','vendor'];

    /**
     * BatchFilters constructor.
     * @param string $cache_key
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->cache_key = 'batch_filters_'.Auth::user()->id;
    }

    /**
     * @param $builder
     * @return mixed
     */
    protected function status()
    {
        $statuses = collect(array_keys($this->request->filters['status']));
//dump($statuses);
        //'received','lab','inventory','sold','rejected','destroyed',

//        list($inv_statuses, $statuses) = $statuses->partition(function ($status) {
//            return in_array($status, ['inventory','sold']);
//        });


//dump($inv_statuses);
//dump($statuses);
//        $rt = $statuses->intersect();

//        if($inv_statuses->count()<2) {
//            $operator = ($inv_statuses->first() == 'inventory' ? '>' : '=');
//            $this->builder->orWhere('inventory', $operator, 0);
////            $operator = ($status[0] == 'inventory' ? '>' : '=');
////            $this->builder->where('inventory', );
//        }

        if($statuses->count()) {
            $this->builder->whereIn('batches.status', $statuses);
        }

//        $statuses->each(function($status) {

//            switch($status)
//            {
//                case "inventory":
//                case "sold":
//
//
//                    break;
//                default:
//                    $this->builder->where('status', $status);
//                    break;
//            }
//        });

//        if($status[0]=='failed') {
//            return $this->builder->where('status', $status[0]);
//        }


//        $where = ($status[0] == 'inventory' ? 'orWhere' : 'where');
        return $this->builder;
//        return $this->builder->where(function($query) use ($operator, $where) {
//            $query->where('inventory', $operator, 0);
//        });
    }

    protected function in_stock()
    {
        $in_stock = collect(array_keys($this->request->filters['in_stock']));
        if($in_stock->count()>=2) return;
        $operator = ($in_stock->first() == 'Yes' ? '>' : '=');
        return $this->builder->where('inventory', $operator, 0);
    }

    protected function in_metrc()
    {
        return $this->builder->where('in_metrc', $this->request->filters['in_metrc']);
    }

    protected function testing_status()
    {
        $testing_status = array_keys($this->request->filters['testing_status']);

        return $this->builder->where('testing_status', $testing_status[0]);
    }

    protected function name()
    {
        return $this->builder->where('batches.name','like','%'.$this->request->filters['name'].'%');
    }

    protected function batch_id()
    {
        return $this->builder->where(function ($q) {
            $q->where('batches.ref_number','like','%'.$this->request->filters['batch_id'].'%')
                ->orWhere('batches.batch_number','like','%'.$this->request->filters['batch_id'].'%');
        });
    }

    protected function fund_id()
    {
        return $this->builder->where('fund_id', $this->request->filters['fund_id']);
    }

    protected function license_id()
    {
        return $this->builder->where('license_id', $this->request->filters['license_id']);
    }

    protected function category()
    {
        return $this->builder->whereIn('category_id', array_keys($this->request->filters['category']));
    }

    protected function uom()
    {
        return $this->builder->whereIn('uom', array_keys($this->request->filters['uom']));
    }

    protected function vendor()
    {
        return $this->builder->select('batches.*')->join('orders', 'batches.purchase_order_id','=','orders.id')
            ->where('orders.vendor_id', $this->request->filters['vendor']);
    }

    protected function brand()
    {
        return $this->builder->where('brand_id', $this->request->filters['brand']);
    }

}