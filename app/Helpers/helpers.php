<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 7/13/17
 * Time: 13:04
 */


use App\Batch;
use App\Brand;
use App\Fund;
use App\License;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


if (! function_exists('get_grams')) {

    function get_grams($uom)
    {
        if(!empty(config('highline.uom')[$uom])) return config('highline.uom')[$uom];

    }
}



//if (! function_exists('display_weight')) {
//
//    function display_weight($weight, $uom_display='lb')
//    {
//        $rtn_weight=0;
//        switch ($uom_display)
//        {
//            case "lb":
//            case "lbs":
//            case "pound":
//            case "pounds":
//                $rtn_weight = round($weight / config('highline.conversions.grams_per_pound'), 2);
//                break;
//            case "g":
//            case "gs":
//            case "gram":
//            case "grams":
//                $rtn_weight = $weight;
//                break;
//        }
//
//        return $rtn_weight." ".$uom_display;
//
//    }
//}


if (! function_exists('display_currency')) {

    function display_currency($amount=0, $dec=2, $sign=1, $k_seperator=",")
    {
        $amt = number_format($amount, $dec, ".", $k_seperator);
        return ($sign?"$":'').$amt;
//        if($amt<0) {
//            return "-$".abs($amt);
//        } else {
//            return "$".$amt;
//        }

    }
}

if (! function_exists('display_currency_no_sign')) {

    function display_currency_no_sign($amount=0, $dec=2)
    {
        return display_currency($amount, $dec, 0, "");
    }
}

if (! function_exists('display_status')) {

    function display_status($status)
    {
        switch ($status) {
            case "basket-pending":
//                return '<a href="'.route('baskets.show', ['id'=>$model->id]).'">Basket</a>';
                return 'Basket Pending';
                break;
            default:
                return ucwords($status);
        }
    }
}

if (! function_exists('status_class')) {

    function status_class($status)
    {
        switch ($status) {
            case 'pending':
                return 'info';
                break;
            case "open":
                return 'success';
            break;
            case "inventory":
            case "Inventory":
            case "Passed":
                return 'primary';
                break;
            case "Lab":
            case 'in-transit':
            case 'In-Testing':
                return 'warning';
                break;
            case 'sold':
            case 'closed';
            case "delivered":
            case "Failed":
                return 'danger';
                break;
            case 'returned':
                return 'default';
                break;
            case 'destroyed':
                return 'pink';
                break;
            case "ready for delivery":
                return 'info';
                break;
            default:
                return 'default';
                break;

        }

        return ucwords($status);
    }
}

if (! function_exists('sold_class')) {

    function sold_class($status)
    {
        if($status=='sold') {
            return "ion-checkmark-circled text-success";
        } else {
            return "ion-close-circled text-danger";
        }
    }
}

if (! function_exists('display_roles')) {

    function display_roles($user)
    {
        return display_list($user->roles);
    }
}

if (! function_exists('display_list')) {

    function display_list($list, $col='name')
    {
        $html_list = '<ul>';
        foreach($list->pluck($col) as $val) {
            $html_list .= "<li>{$val}</li>";
        }
        $html_list .= '</ul>';
        return $html_list;
    }
}

if (! function_exists('display_filters')) {

    function display_filters($filter, $value, $dataset=null)
    {
        $label = ucwords($filter);
        $v = (is_array($value) ? implode(", ", $value) : ucwords($value) );

        switch ($filter)
        {
            case "status":
//                dump();
//                if(is_null($dataset)) break;
////                dd($dataset);
//                $v = $dataset
//                    ->groupBy('status')
//                    ->map(function($coll, $key) {
//                        return ucwords($key);
//                    })
//                    ->implode(', ');
                break;
            case "in_stock":
                $label = "In Stock";
                break;
            case "in_metrc":
                $label = "In Metrc";
                $v = ($value?"Yes":"No");
                break;
            case "testing_status":
                $label = "Testing Status";
                break;
            case "date_preset":
                $label = "Date Preset";
                $v = Carbon::createFromFormat('m-Y', $value)->format('F, Y');
                break;
            case "from_date":
            case "to_date":
                $label = ($filter=='from_date' ? "From Date" : "To Date");
                $v = Carbon::parse($value)->format('m/d/Y');
                break;
            case "vendor":
            case "customer":
                $user = User::find($value);
                $v = $user->name;
                break;
            case "sales_rep":
                $label = "Sales Rep";
                $user = User::find($value);
                $v = ($user?$user->name:'None');
                break;
            case "broker_id":
                $label = "Broker";
                $user = User::find($value);
                $v = ($user?$user->name:'None');
                break;
            case "sale_type":
                $label = "Sale Type";
//                $v = ucwords($value);
                break;
            case "brand":
                $v = Brand::find($value)->name;
                break;
            case "batch_id":
                $v = $value;
                $label = 'Batch/Unique/Pkg ID';
                break;
            case "fund_id":
                $fund = Fund::find($value);
                $v = ($fund?$fund->name:'None');
                $label = 'Funding';
                break;
            case "license_id":
                $license = License::find($value);
                $v = ($license?$license->legal_business_name." - ".$license->number:'None');
                $label = 'License';
                break;
            case "ref_number":
                $label = 'SO#';
                break;
            case "manifest_no":
                $label = 'Manifest#';
                break;

        }

        return "<strong>".$label.": </strong>".$v;

    }
}

if (! function_exists('clean_string')) {

    function clean_string($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}

if (! function_exists('clean_string_strict')) {

    function clean_string_strict($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^ \w]+/', '', $string); // Removes special chars.
    }
}

if (! function_exists('badge_color')) {
    function badge_color($number)
    {
        switch(true)
        {
            case ($number >= 60):
                return 'danger';
                break;
            case ($number >= 30):
                return 'warning';
                break;
            case ($number >= 15):
                return 'info';
                break;
            default:
                return 'success';
                break;
        }

    }
}

if (! function_exists('display_inventory')) {
    function display_inventory($batch, $field='inventory', $display_lb=false)
    {
        $batches=null;
        if($batch instanceof Collection) {
            $batches = $batch;
            $field = ($batch->first()->wt_based ? 'wt_grams' : $field);
            $count = $batch->sum($field);
            $batch = $batch->first();
        } else {
            $field = ($batch->wt_based?'wt_grams':$field);
            $count = $batch->{$field};
        }

        if(empty($batch->inventory)) $count=0;
//dd($count);
        $uom = $batch->uom;
        $min=0;
        switch($batch->category_id)
        {
            case 1:
            case 20:
                if($batch->uom=='lb') {
                    $min = 4;
                } elseif($batch->uom=='g') {
                    $min = 1812;
                }
                break;
            case 11: //pre-roll
                $min = 50;
                break;
            case 22: //pre-pack
                $min = 16;
                break;
            default:
                $min = 0;
                break;
        }

        $str = "<span class='".($count<=$min?"text-danger":"")."'>";

        if($display_lb) {
            if($uom == 'g') {
                $str .= number_format($count / config('highline.uom.lb'), 4) . " lb";
            } else {
                $str .= $count . " <small>" . $uom . "</small>";
            }
        } else {

            if($batch->wt_based) {
                if($batches) {
                    $str .= $count." <small>g</small>";
                } else {
                    $str .= $count." <small>g</small>";
//dump(config('highline.uom'));
//                    $val = ($batch->wt_grams/config('highline.uom')[$batch->uom]);
//                    $str .= "<br><small><i>UOM: $batch->uom</i></small>";
                }

            } else {
                $str .= $count." <small>".$uom."</small>";
            }

            //bulk flower
            if (stristr($batch->category->name, 'bulk') && $batch->uom == 'g') {
                $str .= "<br><small>(" . number_format($count / config('highline.uom.lb'), 4) . " lb)</small>";
            }
        }

        $str .= "</span>";

        return $str;
    }
}

if (! function_exists('is_round')) {
    function is_round($value)
    {
        return is_numeric($value) && intval($value) == $value;
    }
}

if (! function_exists('display_potency_results')) {
    function display_potency_results($batch)
    {

        if($batch instanceof Batch) {
//            dump($batch->id);
            return $batch->present()->thc_potency();
        } else { //collection

            if($batch->min('COASourceBatch.thc') != $batch->max('COASourceBatch.thc')) {
                return $batch->min('COASourceBatch.thc')."% - ".$batch->max('COASourceBatch.thc')."%";
            } else {
                if(!$batch->max('COASourceBatch.thc')) return '';
                return $batch->max('COASourceBatch.thc')."%";
            }
        }

    }
}

if (! function_exists('display_coa_icons')) {
    function display_coa_icons($batch)
    {
        $str = "";
        if($batch->coa_batch) {
            $str .= '<i class="ion-ribbon-b"></i>';
        }
        if($batch->coa_sample) {
            $str .= '<i class="ion-beaker"></i>';
        }
        return $str;
    }
}


