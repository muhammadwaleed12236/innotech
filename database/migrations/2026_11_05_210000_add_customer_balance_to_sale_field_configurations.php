<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\SaleFieldConfiguration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exists = DB::table('sale_field_configurations')->where('field_key', 'summary_customer_balance')->exists();
        if (!$exists) {
            $now = now();
            DB::table('sale_field_configurations')->insert([
                'field_key' => 'summary_customer_balance',
                'label' => 'Customer Previous & Total Balance',
                'section' => 'Order Summary & Totals',
                'field_type' => 'summary',
                'description' => 'Show customer previous balance and total closing balance in the invoice summary.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 280,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            SaleFieldConfiguration::clearCache();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('sale_field_configurations')->where('field_key', 'summary_customer_balance')->delete();
        SaleFieldConfiguration::clearCache();
    }
};
