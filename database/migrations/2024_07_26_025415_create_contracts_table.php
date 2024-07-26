<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('bitrix_id');
            $table->integer('client_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->string('title')->nullable();
            $table->string('balance')->nullable();
            $table->string('recommended_payment')->nullable();
            $table->string('previous_period_amount')->nullable();
            $table->string('type')->nullable();
            $table->string('number')->nullable();
            $table->string('date')->nullable();
            $table->string('phone')->nullable();
            $table->string('create_deals')->nullable();
            $table->string('export_start_date')->nullable();
            $table->string('export_week_days')->nullable();
            $table->string('export_frequency')->nullable();
            $table->string('payment_total')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('export_total_count')->nullable();
            $table->string('attorney_date')->nullable();
            $table->string('price')->nullable();
            $table->string('price_fruits_vegetables')->nullable();
            $table->string('price_bakery')->nullable();
            $table->string('price_dairy')->nullable();
            $table->string('price_used_oil')->nullable();
            $table->string('price_grocery')->nullable();
            $table->string('price_waste')->nullable();
            $table->string('other')->nullable();
            $table->string('city')->nullable();
            $table->string('status')->nullable();
            $table->string('shipment')->nullable();
            $table->string('process')->nullable();
            $table->string('supplier_registered')->nullable();
            $table->string('retailer')->nullable();
            $table->string('region')->nullable();
            $table->string('balance_status')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('contracts');
    }
};
