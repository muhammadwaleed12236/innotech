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
        if (!Schema::hasTable('product_batches')) {
            Schema::create('product_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('variant_key')->nullable();
                $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
                $table->string('batch_no');
                $table->date('mfg_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->decimal('qty', 15, 2)->default(0);
                $table->decimal('cost_price', 15, 2)->default(0);
                $table->string('remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_serials')) {
            Schema::create('product_serials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('variant_key')->nullable();
                $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
                $table->unsignedBigInteger('batch_id')->nullable();
                $table->string('serial_number')->unique();
                $table->string('status')->default('available'); // available, sold, returned
                $table->decimal('cost_price', 15, 2)->default(0);
                $table->string('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_serials');
        Schema::dropIfExists('product_batches');
    }
};
