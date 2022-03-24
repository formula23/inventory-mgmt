<?php

use App\TaxRate;
use Illuminate\Database\Seeder;

class TaxRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tax_rates = collect([
            [
                "category"=>"Cultivation",
                "name"=>"Cultivation Tax 2018 - Flower",
                "amount"=>"148",
                "type"=>"wt",
                "uom"=>"lb",
                "is_active"=>"0",
            ],
            [
                "category"=>"Cultivation",
                "name"=>"Cultivation Tax 2018 - Trim",
                "amount"=>"44",
                "type"=>"wt",
                "uom"=>"lb",
                "is_active"=>"0",
            ],
            [
                "category"=>"Cultivation",
                "name"=>"Cultivation Tax 2020 - Flower",
                "amount"=>"154.4",
                "type"=>"wt",
                "uom"=>"lb",
                "is_active"=>"0",
            ],
            [
                "category"=>"Cultivation",
                "name"=>"Cultivation Tax 2020 - Trim",
                "amount"=>"45.92",
                "type"=>"wt",
                "uom"=>"lb",
                "is_active"=>"0",
            ],
            [
                "category"=>"Cultivation",
                "name"=>"Cultivation Tax 2022 - Flower",
                "amount"=>"161.28",
                "type"=>"wt",
                "uom"=>"lb",
                "is_active"=>"1",
            ],
            [
                "category"=>"Cultivation",
                "name"=>"Cultivation Tax 2022 - Trim",
                "amount"=>"48",
                "type"=>"wt",
                "uom"=>"lb",
                "is_active"=>"1",
            ],
            [
                "category"=>"Excise",
                "name"=>"Excise 2018",
                "amount"=>"24",
                "type"=>"pct",
                "uom"=>null,
                "is_active"=>"0",
            ],
            [
                "category"=>"Excise",
                "name"=>"Excise 2020",
                "amount"=>"27",
                "type"=>"pct",
                "uom"=>null,
                "is_active"=>"1",
            ],
            [
                "category"=>"Sales",
                "name"=>"Sales Tax CA",
                "amount"=>"9.25",
                "type"=>"pct",
                "uom"=>null,
                "is_active"=>"0",
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); // disable foreign key constraints


        TaxRate::truncate();

        $tax_rates->each(function($item) {
            TaxRate::create($item);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); // disable foreign key constraints

//        dd($conversions);
    }
}
