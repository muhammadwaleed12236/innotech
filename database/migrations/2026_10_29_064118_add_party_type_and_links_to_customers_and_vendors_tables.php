<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'party_type')) {
                $table->string('party_type', 20)->default('customer')->after('customer_type');
            }
            if (!Schema::hasColumn('customers', 'linked_vendor_id')) {
                $table->unsignedBigInteger('linked_vendor_id')->nullable()->after('party_type');
            }
        });

        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'party_type')) {
                $table->string('party_type', 20)->default('vendor')->after('name');
            }
            if (!Schema::hasColumn('vendors', 'linked_customer_id')) {
                $table->unsignedBigInteger('linked_customer_id')->nullable()->after('party_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'linked_vendor_id')) {
                $table->dropColumn('linked_vendor_id');
            }
            if (Schema::hasColumn('customers', 'party_type')) {
                $table->dropColumn('party_type');
            }
        });

        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'linked_customer_id')) {
                $table->dropColumn('linked_customer_id');
            }
            if (Schema::hasColumn('vendors', 'party_type')) {
                $table->dropColumn('party_type');
            }
        });
    }
};
