<?php

/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 11/30/17
 * Time: 21:54
 */

namespace App\Http\ViewComposers\Batches;

use App\Category;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class IndexComposer
{

    public function compose(View $view)
    {
        $categories = Category::all();
        $cat_map = $categories->pluck('name','id')->toArray();

        $inventory_by_cat = new Collection();

        $filtered_inventory_value=0;
        $total_transit_value=0;
        $cult_tax_liability=0;

//dd($view->batches->groupBy(['category_id']));
        foreach($view->batches->groupBy(['category_id']) as $category_id => $batches)
        {
//dd($batches->sortBy('name'));

//            dd($batches->groupBy('uom'));

//            $total_grams=0;
//            $batches->each(function ($item, $key) use ($total_grams) {
//
//                dd($item->inventory * config('highline.uom.'.$item->uom));
//
//            });
//dd($batches);

            $collection = collect([
                'category_id'=>$category_id,
                'name'=>$cat_map[$category_id],
//                    'uom'=>$uom,
                'inventory'=>$batches->sum('inventory'),
                'transit'=>$batches->sum('transit'),
                'sold'=>$batches->sum('sold'),
                'batches'=>$batches->sortBy('name')->groupBy(['brand_name']),
            ]);

            $collection->put('inventory_value', $batches->sum(function ($batch) {
                return ($batch->inventory * $batch->unit_price);
            }));

            $collection->put('tax_liability', $batches->sum(function ($batch) {
                return ($batch->inventory * $batch->unit_tax_amount);
            }));

            $filtered_inventory_value += $collection['inventory_value'];
            $cult_tax_liability += $collection['tax_liability'];

            $inventory_by_cat->push($collection);



//            foreach($batches->orderBy('name') as $batch)
//            {
//                $collection = collect([
//                    'category_id'=>$category_id,
//                    'name'=>$cat_map[$category_id],
////                    'uom'=>$uom,
//                    'inventory'=>$batches->sum('inventory'),
//                    'transit'=>$batches->sum('transit'),
//                    'sold'=>$batches->sum('sold'),
//                    'batches'=>$batches->groupBy(['brand_name']),
//                ]);
//
//                $collection->put('inventory_value', $batches->sum(function ($batch) {
//                    return ($batch->inventory * $batch->unit_price);
//                }));
//
//                $total_inventory_value += $collection['inventory_value'];
//
//                $inventory_by_cat->push($collection);
//            }
        }
//dd($inventory_by_cat);
        $view
            ->with('filtered_inventory_value', $filtered_inventory_value)
            ->with('total_transit_value', $total_transit_value)
            ->with('inventory_by_category', $inventory_by_cat)
            ->with('cult_tax_liability', $cult_tax_liability)
            ->with('cat_map', $cat_map);

    }

}