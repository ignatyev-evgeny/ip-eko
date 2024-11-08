<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('client')->after('client_id')->nullable();
            $table->string('shop')->after('client')->nullable();
            $table->string('shop_address')->after('shop')->nullable();
        });
    }

    public function down(): void {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('client');
            $table->dropColumn('shop');
            $table->dropColumn('shop_address');
        });
    }
};
