<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Ultraware\Roles\Models\Permission;
use Ultraware\Roles\Models\Role;

class CustomersController extends Controller
{
    protected $roles = null;
    protected $permissions = null;

    public function __construct()
    {
        parent::__construct();

        $this->roles = Role::orderBy('level', 'desc')->get();
        $this->permissions = Permission::all();
    }

    public function index()
    {

        if(Gate::denies('users.index')) {
            flash('Access Denied!')->error();
            return back();
        }

        $users = User::customers()->with(['roles','license_types'])->orderBy('name')->get();

        return view('users.index', compact('users'));

    }

}
