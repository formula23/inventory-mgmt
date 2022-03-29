<?php

use Illuminate\Database\Seeder;
use Ultraware\Roles\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); // disable foreign key constraints

        Role::truncate();

        Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'level' => 100,
        ]);

        Role::create([
            'name' => 'Buyer',
            'slug' => 'buyer',
            'level' => 70,
        ]);

        Role::create([
            'name' => 'Sales Rep',
            'slug' => 'salesrep',
            'level' => 50,
        ]);

        Role::create([
            'name' => 'Vendor',
            'slug' => 'vendor',
            'level' => 40,
        ]);

        Role::create([
            'name' => 'Customer',
            'slug' => 'customer',
            'level' => 20,
        ]);

        Role::create([
            'name' => 'Manager',
            'slug' => 'manager',
            'level' => 80,
        ]);

        Role::create([
            'name' => 'Sales Manager',
            'slug' => 'sales_manager',
            'level' => 60,
        ]);

        Role::create([
            'name' => 'Bud Tender',
            'slug' => 'bud_tender',
            'level' => 20,
        ]);

        Role::create([
            'name' => 'Broker',
            'slug' => 'broker',
            'level' => 10,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); // enable foreign key constraints
    }
}
