<?php

use App\Category;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); // disable foreign key constraints

        Category::truncate();

        Category::create(['name'=>'Bulk Flower', 'is_active'=>'1']);
        Category::create(['name'=>'Shatter', 'is_active'=>'1']);
        Category::create(['name'=>'Budder', 'is_active'=>'0']);
        Category::create(['name'=>'Crumble', 'is_active'=>'1']);
        Category::create(['name'=>'Slab', 'is_active'=>'0']);
        Category::create(['name'=>'Trim', 'is_active'=>'1']);
        Category::create(['name'=>'Shake', 'is_active'=>'1']);
        Category::create(['name'=>'Nug', 'is_active'=>'0']);
        Category::create(['name'=>'Cartridges', 'is_active'=>'1']);
        Category::create(['name'=>'Vapes', 'is_active'=>'1']);
        Category::create(['name'=>'Pre-Rolls', 'is_active'=>'1']);
        Category::create(['name'=>'Strips', 'is_active'=>'0']);
        Category::create(['name'=>'THCa Isolate', 'is_active'=>'1']);
        Category::create(['name'=>'Live Resin', 'is_active'=>'1']);
        Category::create(['name'=>'Batteries', 'is_active'=>'0']);
        Category::create(['name'=>'Topicals', 'is_active'=>'0']);
        Category::create(['name'=>'Packaged 1/8', 'is_active'=>'1']);
        Category::create(['name'=>'Packaged 1/4', 'is_active'=>'1']);
        Category::create(['name'=>'Packaged 1/2', 'is_active'=>'0']);
        Category::create(['name'=>'Smalls', 'is_active'=>'1']);
        Category::create(['name'=>'Packaged Grams', 'is_active'=>'1']);
        Category::create(['name'=>'Pre-Packaged', 'is_active'=>'1']);
        Category::create(['name'=>'Wax', 'is_active'=>'1']);
        Category::create(['name'=>'Bulk Partials', 'is_active'=>'1']);
        Category::create(['name'=>'Distillate', 'is_active'=>'1']);
        Category::create(['name'=>'Services', 'is_active'=>'1']);
        Category::create(['name'=>'Tax', 'is_active'=>'1']);
        Category::create(['name'=>'Bulk Flower - COA', 'is_active'=>'1']);
        Category::create(['name'=>'Bulk Partials - COA', 'is_active'=>'1']);
        Category::create(['name'=>'Lab Testing', 'is_active'=>'1']);
        Category::create(['name'=>'Pre-Roll Material', 'is_active'=>'1']);
        Category::create(['name'=>'Extracts', 'is_active'=>'1']);
        Category::create(['name'=>'Frost Nugs', 'is_active'=>'1']);
        Category::create(['name'=>'Pending Waste', 'is_active'=>'1']);
        Category::create(['name'=>'Expired', 'is_active'=>'1']);
        Category::create(['name'=>'Bulk Samples', 'is_active'=>'1']);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); // enable foreign key constraints
    }
}
