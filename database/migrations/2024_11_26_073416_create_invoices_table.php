<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->boolean('generated')->default(false);
            $table->boolean('sent')->default(false);
            $table->boolean('paid')->default(false);
            $table->dateTime('date_created');
            $table->dateTime('date_generated')->nullable();
            $table->dateTime('date_sent')->nullable();
            $table->dateTime('date_paid')->nullable();
            $table->string('file_url')->nullable();

            $table->index('generated');
            $table->index('sent');
            $table->index('paid');


        });
    }

    public function down(): void {
        Schema::dropIfExists('invoices');
    }
};
