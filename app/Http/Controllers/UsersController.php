<?php

namespace App\Http\Controllers;

use App\Filters\PurchaseOrderFilters;
use App\License;
use App\PurchaseOrder;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\LicenseType;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Ultraware\Roles\Models\Permission;
use Ultraware\Roles\Models\Role;

class UsersController extends Controller
{
    protected $roles = null;
    protected $permissions = null;

    public function __construct()
    {
        parent::__construct();

        $this->roles = Role::orderBy('level', 'desc')->get();
        $this->permissions = Permission::all();
    }

    /**
     * Display a listing of the resource.
     *
     * @param UserRepositoryInterface $userRepositoryInterface
     * @return \Illuminate\Http\Response
     */
    public function index(UserRepositoryInterface $userRepositoryInterface)
    {
        if(Gate::denies('users.index')) {
            flash('Access Denied!')->error();
            return back();
        }

        $users = $userRepositoryInterface->all();

        return view('users.index', compact('users'));
    }

    public function type(Request $request, $type, UserRepositoryInterface $userRepositoryInterface)
    {
        $users = $userRepositoryInterface->{$type}()->get();
        return view('users.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Gate::denies('users.create')) {
            flash('Access Denied!')->error();
            return back();
        }

        $redirect_to = null;
        $active_role = request('role');

        $license_types = LicenseType::orderBy('name')->pluck('name', 'id');
//        $license_types = LicenseType::all();
//dd($license_types);
        $active_role_id=null;
        if($active_role) {
            $role = Role::select('id')->where('name', $active_role)->first();
            if(!empty($role)) {
                $active_role_id = $role->id;
            }
            $redirect_to = \URL::previous();
        }

        return view('users.create', compact('active_role', 'active_role_id', 'redirect_to', 'license_types'))
            ->with('roles', $this->roles)
            ->with('permissions', $this->permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, UserRepositoryInterface $userRepositoryInterface)
    {
        if(Gate::denies('users.create')) {
            flash('Access Denied!')->error();
            return back();
        }

        $rules = array(
            'name' => 'required|min:2',
//            'email' => 'required|email|unique:users',
//            'phone' => 'required|integer|digits:10',
//            'password' => 'required|min:6|confirmed',
        );

        $validator = Validator::make($request->all(), $rules);

//        dd($validator->all());

        // process the login
        if ($validator->fails()) {
            return redirect(route('users.create'))
                ->withErrors($validator)
                ->withInput($request->except('password'));
        } else {
            // store

            try {

                $request_data = request(['name','email','phone','password', 'details','pin']);
                if(empty($request_data['phone']))$request_data['phone']='3100000000';
                if(empty($request_data['email']))$request_data['email']=str_random(4)."@".str_random(4).".com";
                if(empty($request_data['password'])) $request_data['password']=str_random(16);

    //            dd($request->all());

                if(empty($roles = request('roles'))) {
                    throw new \Exception('Select a role');
                }

                $license=null;
                if(!empty($request->get('license_type_id'))) {
                    $license_data = [
                        'license_type_id'=>$request->input('license_type_id'),
                        'number'=>$request->input('number'),
                        'valid'=>$request->input('valid'),
                        'expires'=>$request->input('expires'),
                        'link'=>$request->input('link'),
                        'legal_business_name'=>$request->input('legal_business_name'),
                        'premise_address'=>$request->input('premise_address'),
                        'premise_city'=>$request->input('premise_city'),
                        'premise_zip'=>$request->input('premise_zip'),
                    ];

                    $license = License::create($license_data);
                }

                $user = $userRepositoryInterface->create($request_data, $roles, request('permissions'), request('license_types'), $license);

                flash()->success('Successfully created user: '.$user->name);

                return redirect( ( request('redirect_to') ? request('redirect_to') : route('users.index') ) );

            } catch(\Exception $e) {

                flash()->error($e->getMessage());
                return back();

            }

        }

    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show(User $user, PurchaseOrderFilters $purchaseOrderFilters)
    {
        if(Gate::denies('users.view')) {
            flash('Access Denied!')->error();
            return back();
        }

        $purchaseOrderFilters->setFilters([
//            'status'=>[
//                'open'=>'Open',
//                'closed'=>'Closed'
//            ],
            'date_preset' => Carbon::today()->format('m-Y'),
        ]);

//        $purchase_orders = PurchaseOrder::where('vendor_id', $user->id)
//            ->filters($purchaseOrderFilters)
//            ->with('batches.order_details_cog')
//            ->with('batches.children_batches')
//            ->get();
//        dd($purchase_orders);

        $purchase_orders = PurchaseOrder::where('vendor_id', $user->id)
            ->with('batches.category')
            ->with('batches.order_details_cog.sale_order.customer')
            ->with('batches.children_batches')
            ->with('batches.children_batches.category')
            ->with('batches.children_batches.order_details_cog.sale_order.customer')
            ->with('batches.children_batches.children_batches.category')
            ->with('batches.children_batches.children_batches.order_details_cog.sale_order.customer')
            ->with('batches.children_batches.children_batches.children_batches.category')
            ->with('batches.children_batches.children_batches.children_batches.order_details_cog.sale_order.customer')
            ->with(['batches.transfer_logs'=>function($qry) {
                $qry->where('type','reconcile');
            }])
            ->with(['batches.children_batches.transfer_logs'=>function($qry) {
                $qry->where('type','reconcile');
            }])
            ->with(['batches.children_batches.children_batches.transfer_logs'=>function($qry) {
                $qry->where('type','reconcile');
            }])
            ->orderBy('txn_date', 'desc')
//            ->where('id',3784)
            ->filters($purchaseOrderFilters)
            ->get();

        $filters = $purchaseOrderFilters->getFilters()->toArray();
//dd($purchase_orders->get(0)->batches->get(0)->children_batches);
//        dd($purchase_orders->first()->batches->first());

        $created_batches = collect();
//        dd($user->purchase_orders->first()->batches->first());

        return view('users.show', compact('user', 'purchase_orders', 'filters', 'created_batches'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit(User $user)
    {
        if(Gate::denies('users.edit')) {
            flash('Access Denied!')->error();
            return back();
        }

        $user->load('licenses.license_type');
//dd($user);
        $license_types = LicenseType::orderBy('name')->pluck('name', 'id');

        return view('users.edit', compact('user','license_types'))
            ->with('roles', $this->roles)
            ->with('permissions', $this->permissions);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(Request $request, User $user)
    {
        if(Gate::denies('users.edit')) {
            flash('Access Denied!')->error();
            return back();
        }

        $rules = array(
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'required|regex:/^(\d{3})-(\d{3})-(\d{4})$/',
            'password' => 'sometimes|nullable|min:6|confirmed',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect(route('users.edit', $user->id))
                ->withErrors($validator)
                ->withInput($request->except(['password','password_confirmation']));
        } else {

            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->phone = $request->get('phone');
            $user->details = $request->get('details');
            $user->active = $request->get('active');
            $user->pin = $request->get('pin');
            if($password = $request->get('password')) {
                $user->password = $password;
            }
//            dump($request->get('password'));
//            dump($password);
//            dump($user->password);
//dd($user);
            $user->roles()->sync($request->get('roles'));
            $user->userPermissions()->sync($request->get('permissions'));
            $user->license_types()->sync($request->get('license_types'));
            $user->save();

            flash()->success('Successfully updated user');

            return redirect(route('users.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return back();
    }
}
