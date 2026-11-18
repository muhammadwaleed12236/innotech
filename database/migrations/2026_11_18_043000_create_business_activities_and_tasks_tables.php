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
        if (!Schema::hasTable('business_activities')) {
            Schema::create('business_activities', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('module', 50)->index(); // sales, purchases, inventory, finance, partners, users, stock
                $table->string('activity_type', 100)->index(); // sale_created, purchase_created, etc.
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('icon', 100)->default('fa-bell');
                $table->string('badge_color', 50)->default('primary');
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_name')->nullable();
                $table->string('reference_no', 100)->nullable()->index();
                $table->decimal('amount', 15, 2)->nullable();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('warehouse_id')->nullable()->index();
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->string('action_url')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['module', 'created_at']);
                $table->index(['created_at']);
            });
        }

        if (!Schema::hasTable('dashboard_tasks')) {
            Schema::create('dashboard_tasks', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
                $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
                $table->date('due_date')->nullable();
                $table->unsignedBigInteger('assigned_to')->nullable()->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'due_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_tasks');
        Schema::dropIfExists('business_activities');
    }
};
