<?php

namespace App;

use App\Presenters\PresentableTrait;
use Illuminate\Support\Facades\DB;
use Ultraware\Roles\Traits\HasRoleAndPermission;
use Ultraware\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasRoleAndPermissionContract
{
    use Notifiable, HasRoleAndPermission, PresentableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'details',
    ];

    protected $dates = [
        'first_order',
        'last_order'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pin',
    ];

    protected $casts = [
        'details' => 'json'
    ];

    protected $presenter = 'App\Presenters\User';

    /**
     * @param $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getNameAttribute($value)
    {
        return trim($value);
    }

    /**
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * @param $value
     */
    public function setPinAttribute($value)
    {
        $this->attributes['pin'] = bcrypt($value);
    }

    /**
     * @param $value
     */
    public function setPhoneAttribute($value)
    {
        $phone = preg_replace('/\D+/', '', $value);
        $this->attributes['phone'] = ( ( ! preg_match('/^\+1/', $phone) && !is_null($phone) ) ? "+1".$phone : $phone );
    }

    /**
     * @param $value
     * @return string
     */
    public function getPhoneAttribute($value)
    {
        $phone = substr($value, 2);

        if( preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $phone,$matches ) )
        {
            $result = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
            return $result;
        }
        return $this->phone;
    }

    public function getDisplayNameAttribute($value)
    {
        $name = "";
        if( ! empty($this->details['business_name'])) {
            $name .= $this->name.'<br>';
            $name .= '<small><i>'.$this->details['business_name']."</i></small>";
            return $name;
        } else {
            return $this->name;
        }
    }

    /**
     * @param $value
     * @return float
     */
    public function getOutstandingBalanceAttribute($value)
    {
        return $value/100;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function batch_pickups()
    {
        return $this->hasMany(BatchPickup::class)->with('batch');

    }

    public function sales_commission_details()
    {
        return $this->hasMany(SalesCommissionDetail::class, 'sales_rep_id');
    }

    public function cultivated_batches()
    {
        return $this->hasMany(Batch::class, 'cultivator_id');
    }

    public function tested_batches()
    {
        return $this->hasMany(Batch::class, 'testing_laboratory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_orders()
    {
        return $this->hasMany(PurchaseOrder::class, 'vendor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sale_orders()
    {
        return $this->hasMany(SaleOrder::class, 'customer_id');
    }

    public function first_sale_order()
    {
        return $this->hasOne(SaleOrder::class, 'customer_id')->orderBy('txn_date');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales_rep_orders()
    {
        return $this->hasMany(SaleOrder::class, 'sales_rep_id');
    }

    public function created_sales_commissions()
    {
        return $this->hasMany(SalesCommission::class, 'user_id');
    }

    public function my_sales_commissions()
    {
        return $this->hasMany(SalesCommission::class, 'sales_rep_id');
    }

    public function license_types()
    {
        return $this->belongsToMany(LicenseType::class, 'license_type_user', 'user_id', 'license_type_id')->withTimestamps();
    }

    public function vault_logs()
    {
        return $this->hasMany(VaultLog::class);
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function cultivation_license()
    {
        return $this->licenses()->where('license_type_id', 1);
    }


    public function roles()
    {
        //override roles() relationship in trait: HasRoleAndPermission
        return $this->belongsToMany(config('roles.models.role'), 'role_user', 'user_id', 'role_id')->withTimestamps();
    }

    public function scopeSalesReps($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('slug', 'salesrep');
        });
    }

    public function scopeCustomers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('slug', 'customer');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeTestingLaboratory($query)
    {
        return $query->whereHas('license_types', function ($q) {
            $q->where('name', 'Testing Laboratory');
        })->orderBy('name');
    }

//    public function activity_logs()
//    {
//        return $this->hasMany(ActivityLog::class);
//    }


    public function scopeWithAndWhereHas($query, $relation, $constraint) {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWithOutstandingBalance($query)
    {
        return $query->select('users.*',\DB::raw('sum(orders.balance) as outstanding_balance'))
            ->join('orders', 'users.id', '=', 'orders.customer_id')
            ->with(['sale_orders' => function ($query) {
                $query->where('balance','!=',0)
                ->with('transactions');
            }])
            ->groupBy('users.id')
            ->orderBy('outstanding_balance', 'desc');
    }

    public function all_customers_ordered_last()
    {
        return static::customers()
            ->select('users.id', 'users.name',
                DB::raw('min(orders.txn_date) as first_order'),
                DB::raw('max(orders.txn_date) as last_order'),
                DB::raw('count(orders.id) as number_of_orders'),
                DB::raw('sum(orders.subtotal/100) as total_order_value'),
                DB::raw('datediff(now(), max(orders.txn_date)) AS `days_last_order`')
            )
            ->where('active', 1)
            ->join('orders','users.id','=','orders.customer_id')
            ->groupBy('users.id')
            ->orderBy('days_last_order', 'desc')
            ->get();
    }

    public function hasSalesCommForPeriod($start_date, $end_date)
    {
        $sales_commissions = static::my_sales_commissions()
            ->where('period_start', $start_date->toDateString())
            ->where('period_end', $end_date->toDateString());
        return ($sales_commissions->count()?true:false);
    }

    static public function search($q)
    {
        return static::query()
            ->where(function($qry) use ($q) {
                $qry->where('users.name', 'like', '%'.$q.'%')
                    ->orWhere('users.email', 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.business_name\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.region\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.address\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.address2\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.contact_name\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.mb_license_number\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.lab_license_number\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.med_license_number\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.mfg_license_number\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.rec_license_number\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.cult_med_license_number\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.cult_rec_license_number\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.distro_med_license_number\')) AS CHAR)'), 'like', '%'.$q.'%')
                    ->orWhere(DB::raw('cast(json_unquote(json_extract(details, \'$.distro_rec_license_number\')) AS CHAR)'), 'like', '%'.$q.'%');
            })
            ->withBalance();
    }



}
