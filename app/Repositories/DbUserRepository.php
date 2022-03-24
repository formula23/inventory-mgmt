<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 6/28/17
 * Time: 01:27
 */

namespace App\Repositories;

use App\License;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\UserRepositoryInterface;

class DbUserRepository implements UserRepositoryInterface
{

    public function all($with=['roles','license_types'])
    {
        return User::with($with)->get();
    }

    public function user()
    {
        return Auth::user();
    }

    public function create($data, $roles=[], $permissions=[], $license_types=[], License $license = null)
    {
        $user = User::create($data);
        $user->roles()->attach($roles);
        $user->userPermissions()->attach($permissions);
        $user->license_types()->attach($license_types);
        if($license) $user->licenses()->save($license);

        return $user;
    }

    public function buyers($active=1)
    {
        return User::whereHas('roles', function ($q) {
            $q->where('slug', 'buyer');
        })->where('active', $active)->get();
    }

    public function vendors($active=1)
    {
        return User::whereHas('roles', function ($q) {
            $q->where('slug', 'vendor');
        })->where('active', $active)
            ->orderBy('name');
    }

    public function transporters($active=1)
    {
        return User::whereHas('roles', function ($q) {
            $q->where('slug', 'transporter');
        })->where('active', $active)->get();
    }

    public function all_transporters_with_pickups()
    {
        return User::whereHas('roles', function ($q) {
            $q->where('level', '<=', 10);
            })
            ->withAndWhereHas('batch_pickups', function($q) {
//                $q->where('status', '=', 'transit');
            });
    }

    public function my_pickups()
    {
        return $this->all_transporters_with_pickups()->where('id', Auth::user()->id);
    }

    public function customers($active=1)
    {
        $custs = User::whereHas('roles', function ($q) {
            $q->where('slug', 'customer');
        })->orderBy('name');

        if(!is_null($active)) {
            $custs->where('active', $active);
        }

        return $custs;
    }

    public function sales_reps($active=1)
    {
        return User::whereHas('roles', function ($q) {
            $q->where('slug', 'salesrep');
        })->where('active', $active)
            ->orderBy('name');
    }


    public function find($id)
    {
        return User::find($id);
    }
}