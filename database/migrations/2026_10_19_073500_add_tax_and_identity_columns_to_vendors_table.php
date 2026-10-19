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
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'cnic')) {
                $table->string('cnic')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('vendors', 'ntn_number')) {
                $table->string('ntn_number')->nullable()->after('cnic');
            }
            if (!Schema::hasColumn('vendors', 'strn_number')) {
                $table->string('strn_number')->nullable()->after('ntn_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['cnic', 'ntn_number', 'strn_number']);
        });
    }
};
