<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->integer('bitrix_id');
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->string('retailer')->nullable();
            $table->string('address')->nullable();
            $table->string('internal_number')->nullable();
            $table->string('external_number')->nullable();
            $table->string('region')->nullable();
            $table->jsonb('cities')->nullable();
            $table->string('direction')->nullable();
            $table->string('type_point')->nullable();
            $table->string('type_payment')->nullable();
            $table->string('price')->nullable();
            $table->string('contract_with')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('problem')->nullable();
            $table->string('export_frequency')->nullable();
            $table->jsonb('export_days')->nullable();
            $table->string('supplier_status')->nullable();
            $table->string('supplier_tech_status')->nullable();
            $table->jsonb('supplier_free_days')->nullable();
            $table->string('graph_id')->nullable();
            $table->string('price_filter')->nullable();
            $table->timestamps();

            $table->index('bitrix_id');

        });
    }

    public function down(): void {
        Schema::dropIfExists('suppliers');
    }
};
