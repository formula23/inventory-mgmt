<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 2/19/19
 * Time: 08:59
 */

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CultivatorScope implements Scope
{
    protected $license_types = [
        'Cultivator',
        'Microbusiness-Cultivator'
    ];
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereHas('license_types', function ($q) {
            $q->whereIn('name', $this->license_types);
        });
    }
}