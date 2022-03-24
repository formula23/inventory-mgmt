<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TransportersController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        view()->share('title','In-Transit');

    }

    public function index(UserRepositoryInterface $userRepositoryInterface)
    {

        if(Gate::allows('transporters.index.all')) {
            $transporters = $userRepositoryInterface->all_transporters_with_pickups();
        } else {
            $transporters = $userRepositoryInterface->my_pickups();
        }

        $transporters = $transporters->with('batch_pickups')->get();

        return view('transporters.index', compact('transporters'));
    }
}
