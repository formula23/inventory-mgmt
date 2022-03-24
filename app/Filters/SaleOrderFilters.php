<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 12/1/17
 * Time: 01:20
 */

namespace App\Filters;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleOrderFilters extends Filters
{
    protected $filters = ['balance','date_preset','from_date','to_date','customer','user'];

    public function __construct(Request $request)
    {
        if($request->filled('filters.from_date') || $request->filled('filters.to_date')) {
            $request->merge($request->except('filters.date_preset'));
        }

        parent::__construct($request);

        $this->default_filters['date_preset'] = Carbon::now()->format('m-Y');
        $this->cache_key = 'so_filters_'.Auth::user()->id;
    }

    protected function customer_type()
    {
        return $this->builder->whereIn('customer_type', array_keys($this->request->filters['customer_type']));
    }

    protected function balance()
    {
        if($this->request->filters['balance'] == 'yes') {
            return $this->builder->where('balance', '!=', 0);
        }

        return $this->builder->where('balance', 0);
    }

    protected function status()
    {
        return $this->builder->where('status', $this->request->filters['status']);
    }

    protected function customer()
    {
        return $this->builder->where('customer_id', $this->request->filters['customer']);
    }

    protected function date_preset()
    {
        list($m,$y) = explode("-", $this->request->filters['date_preset']);
        return $this->builder->whereMonth('txn_date', $m)->whereYear('txn_date', $y);
    }

    protected function ref_number()
    {
        return $this->builder->where('ref_number', 'like', '%'.$this->request->filters['ref_number'].'%');
    }

    protected function sales_rep()
    {
        $sales_rep = $this->request->filters['sales_rep'];
        if(Auth::user()->hasOneRole('salesrep') && (! Auth::user()->isAdmin())) {
            $sales_rep = Auth::user()->id;
        }
        if($sales_rep=='None') {
            return $this->builder->whereNull('sales_rep_id');
        } else {
            return $this->builder->where('sales_rep_id', $sales_rep);
        }

    }

    protected function broker_id()
    {
        return $this->builder->where('broker_id', $this->request->filters['broker_id']);
    }

    protected function sale_type()
    {
        return $this->builder->whereIn('sale_type', array_keys($this->request->filters['sale_type']));
    }

    protected function sales_comm_paid()
    {
        $where = ($this->request->filters['sales_comm_paid']=='yes'?'whereHas':'whereDoesntHave');
        $this->builder->{$where}('sales_commission_details');
    }

    protected function from_date()
    {
        return $this->builder->whereDate('txn_date', '>=', $this->request->filters['from_date']);
    }

    protected function to_date()
    {
        return $this->builder->whereDate('txn_date', '<=', $this->request->filters['to_date']);
    }

    protected function manifest_no()
    {
        return $this->builder->where('manifest_no', 'like', '%'.$this->request->filters['manifest_no'].'%');
    }
}