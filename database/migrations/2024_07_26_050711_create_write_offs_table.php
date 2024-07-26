<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('write_offs', function (Blueprint $table) {
            $table->id();
            $table->string('external')->nullable();
            $table->string('store')->nullable();
            $table->dateTime('date')->nullable();
            $table->decimal('total_weight', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('counteragent')->nullable();
            $table->string('contract')->nullable();
            $table->string('retailer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('write_offs');
    }
};
