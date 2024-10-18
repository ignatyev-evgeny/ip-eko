<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('write_offs', function (Blueprint $table) {
            $table->longText('comment')->after('status')->nullable();
        });
    }

    public function down(): void {
        Schema::table('write_offs', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
