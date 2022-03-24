<?php

use App\LicenseType;
use Illuminate\Database\Seeder;

class LicenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); // disable foreign key constraints

        LicenseType::truncate();

        collect([
            'Cultivator',
            'Distributor',
            'Distributor-Transport Only',
            'Retailer',
            'Retailer Nonstorefront',
            'Microbusiness-Cultivation',
            'Microbusiness-Distributor',
            'Testing Laboratory',
            'Event Organizer',
            'Manufacturing',
            'Microbusiness-Retailer',
            'Processor',
            'Microbusiness-Manufacturer',
        ])->each(function ($item) {
            LicenseType::create([
                'name'=>$item
            ]);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); // enable foreign key constraints
    }
}
