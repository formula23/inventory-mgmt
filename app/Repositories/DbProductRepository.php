<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 7/10/17
 * Time: 21:41
 */

namespace App\Repositories;


use App\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DbProductRepository implements ProductRepositoryInterface
{

    public function all($with=[], $filters=null)
    {
        $product = Product::query();
        $product->with($with);

        if(Auth::user()->hasRole('transporter')) {
            $product->where('transporter_id', Auth::user()->id)
                ->where('status','!=','sold');
        }

        if($filters) {
           foreach($filters as $col=>$vals)
           {

               if(is_array($vals)) {
                   $product->whereIn($col, array_keys($vals));
               } else {
                   $product->where($col, $vals);
               }
           }
        }

        $product->with(['batch' => function ($q) {
            $q->where('category_id', 1);
        }]);


        $product->orderBy('created_at', 'desc');

        return $product->get();
    }

    public function find($id)
    {
        return Product::find($id);
    }

    public function findByRefNumber($refNumber, $with=[])
    {
        return Product::whereRefNumber($refNumber)->with($with)->firstOrFail();
    }

    public function findOrFail($id)
    {
        return Product::findOrFail($id);
    }
    
}