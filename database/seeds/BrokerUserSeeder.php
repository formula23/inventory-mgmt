<?php

use App\User;
use Illuminate\Database\Seeder;
use Ultraware\Roles\Models\Role;

class BrokerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            'broker' => [
                'Ash > Will',
                'Stroke > Kevon',
                'Stroke > Chapelle',
                'B',
                'B > Ace',
                'B > Jax',
                'Brian',
                'D&D',
                'D&D > E',
                'F',
                'MD',
                'N',
                'S > BOG',
                'S > BODC',
            ],
        ];


        foreach($users as $user_role=>$usernames) {

            $role = Role::whereSlug($user_role)->first();

            foreach($usernames as $username) {
                $user = User::create([
                    'name'=>$username,
                    'email'=>strtolower(preg_replace("/ /","",$username)).'@highline-broker.com',
                    'password'=>'123456',
                    'phone'=>'3105551234',
                ]);
                $user->attachRole($role);
            }

        }
    }
}
