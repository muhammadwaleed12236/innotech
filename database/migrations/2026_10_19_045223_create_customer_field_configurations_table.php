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
        if (!Schema::hasTable('customer_field_definitions')) {
            Schema::create('customer_field_definitions', function (Blueprint $table) {
                $table->id();
                $table->string('field_key')->unique();
                $table->string('label');
                $table->string('field_type')->default('text'); // text, textarea, number, email, phone, date, dropdown, checkbox, url
                $table->boolean('is_system')->default(false);
                $table->string('system_column')->nullable();
                $table->string('section')->default('General'); // Section grouping
                $table->text('default_value')->nullable();
                $table->string('placeholder')->nullable();
                $table->string('help_text')->nullable();
                $table->json('options')->nullable(); // Dropdown options [ {"key":"...", "label":"..."} ] or simple array
                $table->boolean('is_required')->default(false);
                $table->boolean('is_unique')->default(false);
                $table->boolean('is_enabled')->default(true);
                $table->boolean('is_readonly')->default(false);
                $table->boolean('visible_in_list')->default(true);
                $table->boolean('visible_in_view')->default(true);
                $table->boolean('visible_in_create')->default(true);
                $table->boolean('visible_in_edit')->default(true);
                $table->boolean('visible_in_search')->default(false);
                $table->boolean('visible_in_export')->default(true);
                $table->integer('sort_order')->default(0);
                $table->string('view_permission')->nullable();
                $table->string('edit_permission')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('customer_custom_field_values')) {
            Schema::create('customer_custom_field_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->foreignId('field_definition_id')->constrained('customer_field_definitions')->onDelete('cascade');
                $table->string('field_key')->index();
                $table->longText('value')->nullable();
                $table->timestamps();

                $table->unique(['customer_id', 'field_definition_id'], 'cust_field_val_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_custom_field_values');
        Schema::dropIfExists('customer_field_definitions');
    }
};
