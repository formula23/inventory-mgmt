<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 6/28/17
 * Time: 01:33
 */

namespace App\Repositories\Contracts;


use App\License;

interface UserRepositoryInterface
{
    public function all();
    public function find($id);
    public function create($data, $roles, $permissions, $license_types, License $license);
    public function user();
    public function buyers();
    public function transporters();
    public function all_transporters_with_pickups();
    public function my_pickups();
    public function vendors();
    public function customers();
}