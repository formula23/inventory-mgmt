<?php

use Illuminate\Database\Seeder;
use Ultraware\Roles\Models\Permission;
use Ultraware\Roles\Models\Role;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); // disable foreign key constraints

        DB::statement('truncate table permission_role');

        $perm_name_slug = [

            'Dashboard - All Customers' => 'dashboard.allcustomers',
            'Dashboard - New Customers' => 'dashboard.newcustomers',
            'List Users' => 'users.index',
            'View User' => 'users.view',
            'Create User' => 'users.create',
            'Edit User' => 'users.edit',

            'View Customer Users' => 'customers.view',

            'List Purchase Orders'=>'po.index',
            'View Purchase Order'=>'po.show',
            'Create Purchase Order'=>'po.create',
            'Print Purchase Order QR Codes'=>'po.printqr',

            'List Sale Orders' =>'so.index',
            'View Sale Order' => 'so.show',
            'Create Sale Order' => 'so.create',

            'SO Filters - Sales Rep' =>'so.filters.salesrep',

            'List Sales Reps' => 'sales_rep.index',

            'List Batches' => 'batches.index',
            'Show Batches' => 'batches.show',
            'Batches Edit' => 'batches.edit',
            'Batches Sell' => 'batches.sell',
            'Batches Reconcile' => 'batches.reconcile',
            'Batches Transfer' => 'batches.transfer',
            'Batches Show Cost' => 'batches.show.cost',
            'Batches Show Vendor' => 'batches.show.vendor',
            'Batches Show Sold' => 'batches.show.sold',
            'Batches Print Large Label' => 'batches.print.largelabel',

            'List Transporters' => 'transporters.index',
            'List All Transporters' => 'transporters.index.all',

            'List Receivables' => 'accounting.receivables.index',
            'List Receivables Aging' => 'accounting.receivables.aging',

            'View Pre-Pack Logs' => 'prepacklogs.show',
        ];

        //delete non-existing permissions
        Permission::whereNotIn('slug', array_values($perm_name_slug))->delete();

        foreach($perm_name_slug as $permission_name=>$permission_slug)
        {
            try {

                $permission = Permission::firstOrCreate([
                    'name'=>$permission_name,
                    'slug'=>$permission_slug,
                ]);

            } catch(Exception $e)
            {
                dump('error...'.$permission_slug);
            }

            if(in_array($permission_slug, [
                'so.index',
                'so.show',
                'batches.index',
                'batches.show',
            ])) {
                $sales_rep_role = Role::where('slug', 'salesrep')->first();
                $permission->roles()->attach($sales_rep_role->id);
            }

            //manager only
            if(in_array($permission_slug, [
                'so.index',
                'so.show',
                'batches.index',
                'batches.show',
                'batches.print.largelabel',
                'prepacklogs.show',
                'batches.show.vendor',
                'batches.edit',
                'batches.transfer',
                'batches.show.cost',
                'batches.show.sold',
                'batches.transfer',
                'batches.sell',
                'users.index',
                'users.view',
                'so.filters.salesrep',
            ])) {
                $manager_role = Role::where('slug', 'manager')->first();
                $permission->roles()->attach($manager_role->id);
            }


        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); // enable foreign key constraints
    }
}
