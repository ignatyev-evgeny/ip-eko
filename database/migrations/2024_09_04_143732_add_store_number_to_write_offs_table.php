<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('write_offs', function (Blueprint $table) {
            $table->string('take_contract')->default('off')->after('contract');
            $table->string('store_number')->nullable()->after('external');
            $table->string('day_of_week')->nullable()->after('date');
        });
    }

    public function down(): void {
        Schema::table('write_offs', function (Blueprint $table) {
            $table->dropColumn('take_contract');
            $table->dropColumn('store_number');
            $table->dropColumn('day_of_week');
        });
    }
};
