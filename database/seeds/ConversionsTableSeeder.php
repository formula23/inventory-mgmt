<?php

use App\Conversion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conversions = collect([
            [
                "from"=>"lb",
                "to"=>"g",
                "value"=>"453.5924",
                "editable"=>0,
            ],
            [
                "from"=>"lb",
                "to"=>"oz",
                "value"=>"16",
                "editable"=>0,
            ],
            [
                "from"=>"g",
                "to"=>"lb",
                "value"=>"0.00220462",
                "editable"=>0,
            ],
            [
                "from"=>"g",
                "to"=>"oz",
                "value"=>"0.03527399072",
                "editable"=>0,
            ],
            [
                "from"=>"oz",
                "to"=>"lb",
                "value"=>"0.0625",
                "editable"=>0,
            ],
            [
                "from"=>"oz",
                "to"=>"g",
                "value"=>"28.3495",
                "editable"=>0,
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); // disable foreign key constraints

        Conversion::truncate();

        $conversions->each(function($item) {
            Conversion::create($item);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); // disable foreign key constraints

//        dd($conversions);


    }
}
