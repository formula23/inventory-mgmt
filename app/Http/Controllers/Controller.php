<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('auth', ['except' => ['VaultLogController@login','forceLogin']]);

        view()->share('warnings', collect());
        view()->share('title', $this->construct_title()?:'Dashboard');
    }

    protected function construct_title()
    {
        $parts = explode("-", Request::segment(1));
        $parts = array_map('ucwords', $parts);
        return implode(" ", $parts);
    }

}
