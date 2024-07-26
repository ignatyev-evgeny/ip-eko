<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->dateTime('datetime');
            $table->string('number');
            $table->decimal('amount', 10, 2);
            $table->string('counteragent');
            $table->string('counteragent_bank_account');
            $table->string('contract');
            $table->text('payment_purpose');
            $table->string('operation_type');
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
