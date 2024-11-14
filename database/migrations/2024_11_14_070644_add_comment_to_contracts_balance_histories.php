<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contracts_balance_histories', function (Blueprint $table) {
            $table->longText('comment')->after('end_balance')->nullable();
            $table->string('type')->after('id')->nullable();
            $table->integer('type_relation')->after('type')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contracts_balance_histories', function (Blueprint $table) {
            $table->dropColumn('comment');
            $table->dropColumn('type');
            $table->dropColumn('type_relation');
        });
    }
};

