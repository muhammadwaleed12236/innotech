<?php

namespace Database\Seeders;

use App\Services\CustomerFieldService;
use Illuminate\Database\Seeder;

class CustomerFieldConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service = app(CustomerFieldService::class);
        $service->seedDefaultDefinitions();
    }
}
