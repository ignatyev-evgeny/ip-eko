<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('entries', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
            $table->dateTime('datetime')->nullable()->change();
            $table->string('number')->nullable()->change();
            $table->decimal('amount', 10, 2)->nullable()->change();
            $table->string('counteragent')->nullable()->change();
            $table->string('counteragent_bank_account')->nullable()->change();
            $table->string('contract')->nullable()->change();
            $table->text('payment_purpose')->nullable()->change();
            $table->string('operation_type')->nullable()->change();
            $table->string('uuid')->after('id')->unique()->nullable();
        });
    }

    public function down(): void {
        Schema::table('entries', function (Blueprint $table) {
            $table->string('status')->change();
            $table->dateTime('datetime')->change();
            $table->string('number')->change();
            $table->decimal('amount', 10, 2)->change();
            $table->string('counteragent')->change();
            $table->string('counteragent_bank_account')->change();
            $table->string('contract')->change();
            $table->text('payment_purpose')->change();
            $table->string('operation_type')->change();
            $table->dropColumn('uuid');
        });
    }
};
