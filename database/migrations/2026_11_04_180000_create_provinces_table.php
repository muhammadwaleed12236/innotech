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
        if (!Schema::hasTable('provinces')) {
            Schema::create('provinces', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('status')->default('active');
                $table->timestamps();
            });
        } else {
            Schema::table('provinces', function (Blueprint $table) {
                if (!Schema::hasColumn('provinces', 'status')) {
                    $table->string('status')->default('active')->after('name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
