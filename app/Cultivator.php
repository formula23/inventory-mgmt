<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 2/19/19
 * Time: 08:57
 */

namespace App;

use App\Scopes\CultivatorScope;


class Cultivator extends User
{
    protected $table = 'users';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CultivatorScope());
    }

    public static function all($columns = ['*'], $active = 1)
    {
//        parent::all();

        return (new static)
            ->newQuery()
            ->where('active', 1)
            ->with('license_types')
            ->orderBy('name')
            ->get(
            is_array($columns) ? $columns : func_get_args()
        );

//        return static::where('active',1)->get();
    }

}