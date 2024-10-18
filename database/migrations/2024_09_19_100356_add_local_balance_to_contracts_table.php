<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('local_balance', 8, 2)->default(0.00)->after('id');
        });
    }

    public function down(): void {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('local_balance');
        });
    }
};
