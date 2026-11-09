<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'custom_disc_breakdown')) {
                $table->longText('custom_disc_breakdown')->nullable()->after('our_disc_notes');
            }
            if (!Schema::hasColumn('purchases', 'our_disc_breakdown')) {
                $table->longText('our_disc_breakdown')->nullable()->after('custom_disc_breakdown');
            }
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'custom_disc_breakdown')) {
                $table->longText('custom_disc_breakdown')->nullable()->after('our_disc_notes');
            }
            if (!Schema::hasColumn('purchase_items', 'our_disc_breakdown')) {
                $table->longText('our_disc_breakdown')->nullable()->after('custom_disc_breakdown');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['custom_disc_breakdown', 'our_disc_breakdown']);
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['custom_disc_breakdown', 'our_disc_breakdown']);
        });
    }
};
