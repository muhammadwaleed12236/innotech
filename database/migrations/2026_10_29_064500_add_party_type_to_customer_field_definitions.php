<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CustomerFieldDefinition;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('customer_field_definitions')) {
            CustomerFieldDefinition::firstOrCreate([
                'field_key' => 'party_type'
            ], [
                'label'             => 'Party Category (Role)',
                'field_type'        => 'dropdown',
                'is_system'         => true,
                'system_column'     => 'party_type',
                'section'           => 'Basic Information',
                'placeholder'       => 'Select Party Role',
                'help_text'         => 'Select if Customer Only, Vendor Only, or Both',
                'options'           => ['customer' => 'Customer Only', 'both' => 'Both (Customer & Vendor)'],
                'default_value'     => 'customer',
                'is_required'       => true,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => true,
                'visible_in_export' => true,
                'sort_order'        => 15,
            ]);

            \Illuminate\Support\Facades\Cache::forget('customer_field_definitions_all');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customer_field_definitions')) {
            CustomerFieldDefinition::where('field_key', 'party_type')->delete();
            \Illuminate\Support\Facades\Cache::forget('customer_field_definitions_all');
        }
    }
};
