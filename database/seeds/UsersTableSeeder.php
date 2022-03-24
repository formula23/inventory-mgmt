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
                'Dan','Nick',
            ],
            'transporter' => [
                'Chris','Zack','Nikki',
            ],
            'vendor' => [
                'Cannabiotix','PureLife','John Pap'
            ],
            'customer' => [
                'SFVDM','New Age','MedMen','Ben Farms'
            ]
        ];

        foreach($users as $user_role=>$usernames) {

            $role = Role::whereSlug($user_role)->first();

            foreach($usernames as $username) {
                $user = User::create([
                    'name'=>$username.' '.$user_role,
                    'email'=>strtolower(preg_replace("/ /","",$username)).'@highline.com',
                    'password'=>'123456',
                    'phone'=>'3105551234',
                ]);
                $user->attachRole($role);
            }

        }

    }
}
