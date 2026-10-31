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
        if (!Schema::hasTable('cheques')) {
            Schema::create('cheques', function (Blueprint $table) {
                $table->id();
                $table->string('cheque_no')->index();
                $table->string('bank_name');
                $table->date('cheque_date'); // Maturity / Due Date
                $table->decimal('amount', 15, 2);
                
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->unsignedBigInteger('customer_payment_id')->nullable()->index();
                
                // Status: in_hand (received/pending), deposited, cleared (money in bank), bounced
                $table->string('status')->default('in_hand')->index();
                
                // Which company bank account it was deposited / cleared into
                $table->unsignedBigInteger('deposit_account_id')->nullable()->index();
                $table->date('cleared_at')->nullable();
                $table->date('bounced_at')->nullable();
                $table->string('bounce_reason')->nullable();
                $table->text('note')->nullable();
                
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
                $table->foreign('customer_payment_id')->references('id')->on('customer_payments')->cascadeOnDelete();
                $table->foreign('deposit_account_id')->references('id')->on('accounts')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
