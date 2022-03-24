<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 8/12/18
 * Time: 17:02
 */

namespace App\Presenters;


class VaultLog extends Presenters
{
    public function strain_notes_price($new_line="<br>", $price=null)
    {

        $vault_log = $this->entity;

        $str = $this->strain_name();

        if($vault_log->price) {

            $str .= " - ".$price;
        }

        if($vault_log->notes) {
            $str .= $new_line.$vault_log->notes;
        }

        return $str;
    }

    public function strain_name()
    {
        $vault_log = $this->entity;

        $str="";
        $po_date = ($vault_log->batch->purchase_order ? $vault_log->batch->purchase_order->txn_date->format('n/j') : "");

        if($vault_log->strain_name) {

            $str .= $vault_log->strain_name ." (".($vault_log->batch->description ?: $vault_log->batch->name ) .($po_date?" - ".$po_date:"").")";

        } else {

            if($vault_log->batch->description) {

                $str .= $vault_log->batch->description . " (". $po_date . ")";

            } else {

                $str .= $vault_log->batch->name ." (".$po_date .")";
            }
        }

        return $str;
    }


}
