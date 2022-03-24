<?php

use App\License;
use Illuminate\Database\Seeder;

class LicenseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        License::create([
            'license_type_id'=>2, //Distro
            'number'=>'A11-18-0000112-TEMP',
            'legal_business_name'=>'The Pottery',
            'premise_address'=>'5042 Venice Blvd',
            'premise_city'=>'Los Angeles',
            'premise_zip'=>'90019',
            'valid'=>'2018-01-29',
            'expires'=>'2019-08-22',
            'active'=>0,
        ]);

        License::create([
            'license_type_id'=>2, //Distro
            'number'=>'C11-0000347-LIC',
            'legal_business_name'=>'High Line Distribution, Inc.',
            'premise_address'=>'11165 Tennessee Ave.',
            'premise_city'=>'Los Angeles',
            'premise_zip'=>'90064',
            'valid'=>'2019-06-13',
            'expires'=>'2020-06-12',
            'link'=>'https://drive.google.com/file/d/1GTbfNzdmghJUG4DZNTS9QvRffK5FqUi9/view?usp=sharing',
        ]);


        License::create([
            'license_type_id'=>10, //MFG
            'number'=>'CPDH-10002965',
            'legal_business_name'=>'High Line Distribution, Inc.',
            'premise_address'=>'11165 Tennessee Ave.',
            'premise_city'=>'Los Angeles',
            'premise_zip'=>'90064',
            'valid'=>'2019-04-29',
            'expires'=>'2021-04-29',
            'link'=>'https://drive.google.com/file/d/1GTbfNzdmghJUG4DZNTS9QvRffK5FqUi9/view?usp=sharing',
        ]);
    }
}
