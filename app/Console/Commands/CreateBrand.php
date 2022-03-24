<?php

namespace App\Console\Commands;

use App\Brand;
use Illuminate\Console\Command;

class CreateBrand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brand:create {brand_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new brands';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $brand = Brand::create(['name'=>$this->argument('brand_name')]);

        $this->info('Brand id#'.$brand->id.' - '.$brand->name.' created!');

    }
}
