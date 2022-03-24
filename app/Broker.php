<?php

namespace App;

use App\Scopes\BrokerScope;
use Illuminate\Database\Eloquent\Model;
use Ultraware\Roles\Models\Role;

class Broker extends User
{

    protected $table = 'users';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new BrokerScope);
    }

    public function sales_orders()
    {
        return $this->hasMany(Order::class, 'broker_id');
    }

    static public function create_new_broker($name)
    {

        $b = self::create([
            'name'=>$name,
            'email'=>strtolower(preg_replace("/ /","", $name)).'@highline-broker.com',
            'password'=>'123456',
            'phone'=>'3105551234',
        ]);
        $b->attachRole(Role::whereSlug('broker')->first());
        return $b;
    }

}
