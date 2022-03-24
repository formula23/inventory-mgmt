<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ultraware\Roles\Models\Role;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            { name }
                            { email }        
                            { phone : Format: 3105551234 }
                            { role=admin : The User Role [admin, buyer, salesrep, vendor, customer]}
                            { details? : Any additional details needed for this user in JSON format }
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user';

    protected $user = null;
    protected $role = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $password = $this->secret('Password?');
        $password_confirm = $this->secret('Confirm Password');

        if($password != $password_confirm) {
            $this->error('You typed the wrong password! Please try again.');
            return false;
        }

        if( ! $this->role = Role::where('slug', $this->argument('role'))->first()) {
            $this->error('Invalid role!');
            return false;
        }

        $data = [
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => $password,
            'phone' => $this->argument('phone'),
        ];

        $validator = $this->validate($data);

        if($validator->fails()) {
            foreach($validator->errors()->getMessages() as $err) {
                $this->error(implode(", ", $err));
            }
            return false;
        }

        try {
            DB::transaction(function() use ($data) {
                $this->user = User::create($data);
                $this->user->attachRole($this->role);
            });

        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }

        $this->info($this->user->name.' user account created successfully for '.$this->user->email.' with role: '.$this->role->name);

        return;
    }

    private function validate($data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required',
        ]);
    }
}
