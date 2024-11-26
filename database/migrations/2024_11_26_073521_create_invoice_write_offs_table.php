<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoice_write_offs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('write_off_id')->constrained('write_offs')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('invoice_write_offs');
    }
};
