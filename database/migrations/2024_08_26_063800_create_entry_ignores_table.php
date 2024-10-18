<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('entry_ignores', function (Blueprint $table) {
            $table->string('counteragent');
            $table->string('counteragent_bank_account');
        });
    }

    public function down(): void {
        Schema::dropIfExists('entry_ignores');
    }
};
