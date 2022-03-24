<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 7/10/17
 * Time: 21:41
 */

namespace App\Repositories\Contracts;


interface ProductRepositoryInterface
{

    public function all($with, $filter);
    public function find($id);
    public function findOrFail($id);
    
}