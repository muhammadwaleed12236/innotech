<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerFieldDefinition;
use App\Services\CustomerFieldService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('customer_field_definitions')) {
            try {
                // Update any existing license field rows in database to be optional
                DB::table('customer_field_definitions')
                    ->whereIn('field_key', ['license_no', 'drug_license_no', 'license', 'drug_license'])
                    ->orWhere('label', 'LIKE', '%license%')
                    ->orWhere('field_key', 'LIKE', '%license%')
                    ->update([
                        'is_required' => 0,
                        'updated_at'  => now(),
                    ]);

                // Run seeder to ensure license_no and drug_license_no exist as optional definitions
                (new CustomerFieldService())->seedDefaultDefinitions();
                \Illuminate\Support\Facades\Cache::forget('customer_field_definitions_all');
            } catch (\Throwable $e) {
                // Continue safely
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive reversal
    }
};
