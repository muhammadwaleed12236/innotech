<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('invoice_series')) {
            $exists = DB::table('invoice_series')->where('prefix', 'QUOT')->exists();
            if (!$exists) {
                DB::table('invoice_series')->insert([
                    'prefix' => 'QUOT',
                    'next_number' => 1,
                    'padding' => 4,
                    'is_default' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep series data intact
    }
};
