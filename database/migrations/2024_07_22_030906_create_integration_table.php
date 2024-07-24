<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('integration', function (Blueprint $table) {
            $table->id();
            $table->string('accessToken');
            $table->string('refreshToken');
            $table->string('last_update');
        });
    }

    public function down(): void {
        Schema::dropIfExists('integration');
    }
};
