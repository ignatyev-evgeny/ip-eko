<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('integration_sberbank', function (Blueprint $table) {
            $table->id();
            $table->string('client_id');
            $table->string('client_secret');
            $table->text('scope');
            $table->string('access_token');
            $table->string('refresh_token');
            $table->jsonb('response');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('integration_sberbank');
    }
};
