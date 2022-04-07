<?php

use App\User;
use Illuminate\Database\Seeder;
use Ultraware\Roles\Models\Role;

    class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $users = [
            'admin' => [
                'Admin',
            ],
            'vendor' => [
                'Vendor1'
            ],
            'customer' => [
                'Customer1'
            ],
            'manager' => [
                'Manager1'
            ],
            'bud_tender' => [
                'Bud Tender1'
            ],
            'broker' => [
                'Broker1'
            ]
        ];

        foreach($users as $user_role=>$usernames) {

            $role = Role::whereSlug($user_role)->first();

            foreach($usernames as $username) {
                $user = User::create([
                    'name'=>$username.' '.$user_role,
                    'email'=>strtolower(preg_replace("/ /","",$username)).'@inventory.com',
                    'password'=>'123456',
                    'phone'=>'3105551234',
                ]);
                $user->attachRole($role);
            }

        }

    }
}
