<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 7/13/17
 * Time: 12:44
 */

namespace App\Repositories\Contracts;


use App\Repositories\DbOrderRepository;

interface PurchaseOrderRepositoryInterface
{
    public function all();
    public function create($data);
    public function find($id);
}