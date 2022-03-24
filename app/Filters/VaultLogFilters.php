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

class VaultLogFilters extends Filters
{
//    protected $default_filters = ['in_stock'=>['Yes'=>'Yes']];
    protected $filters = ['broker_id','created_at'];

    /**
     * BatchFilters constructor.
     * @param string $cache_key
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->cache_key = __CLASS__.Auth::user()->id;
    }

    /**
     * @param $builder
     * @return mixed
     */
    protected function broker_id()
    {
        return $this->builder->where('broker_id', $this->request->filters['broker_id']);
    }

    protected function date_preset()
    {
        list($m,$y) = explode("-", $this->request->filters['date_preset']);
        return $this->builder->whereMonth('created_at', $m)->whereYear('created_at', $y);
    }

    protected function from_date()
    {
        return $this->builder->whereDate('created_at', '>=', $this->request->filters['from_date']);
    }

    protected function to_date()
    {
        return $this->builder->whereDate('created_at', '<=', $this->request->filters['to_date']);
    }

}