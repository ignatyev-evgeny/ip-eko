<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->string('status', 100);
            $table->dateTime('datetime');
            $table->string('number', 100);
            $table->decimal('amount', 10, 2);
            $table->string('counteragent', 100);
            $table->string('counteragent_bank_account', 100);
            $table->string('contract', 100);
            $table->text('payment_purpose');
            $table->string('operation_type', 100);
            $table->timestamps();

            $table->index('datetime');
            $table->index('number');
            $table->index('counteragent');
            $table->index('contract');
        });
    }

    public function down(): void {
        Schema::dropIfExists('entries');
    }
};
