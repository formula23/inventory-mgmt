<?php

namespace App\Providers;

use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Ultraware\Roles\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @param GateContract $gate
     * @return bool
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies();

        try {

            foreach($this->getPermissions() as $permission) {
                
                $gate->define($permission->slug, function(User $user, $model=null) use ($permission) {

                    if($user->isAdmin()) return true;

                    if(in_array($permission->slug, ['batches.sell', 'batches.release'])) {
                        if($model && $model->transporter_id && $model->transporter_id != $user->id) return false;
                    }

                    if($user->hasRole($permission->roles->pluck('slug')->toArray())) return true;

                    return $user->hasPermission($permission->slug);
                });
            }

//            dd($gate);
        } catch(QueryException $e) {
            return false;
        }

    }

    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
