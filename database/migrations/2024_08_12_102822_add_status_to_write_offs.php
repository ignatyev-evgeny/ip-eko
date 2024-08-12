<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('write_offs', function (Blueprint $table) {
            $table->string('status')->after('id')->default('new');
        });
    }

    public function down(): void {
        Schema::table('write_offs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
