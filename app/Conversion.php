<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    static public function getRate($from, $to)
    {
        return self::where('from', $from)->where('to',$to)->first();
    }

    static public function getRates()
    {
        $conversions = [];

        self::all()->each(function ($item) use (&$conversions) {
            $conversions[$item->from][$item->to] = $item->value;
        });

        return $conversions;

    }

}
