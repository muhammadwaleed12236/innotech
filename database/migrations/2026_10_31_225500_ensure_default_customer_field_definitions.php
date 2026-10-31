<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
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
                (new CustomerFieldService())->seedDefaultDefinitions();
                \Illuminate\Support\Facades\Cache::forget('customer_field_definitions_all');
            } catch (\Throwable $e) {
                // Log or continue silently
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive action
    }
};
