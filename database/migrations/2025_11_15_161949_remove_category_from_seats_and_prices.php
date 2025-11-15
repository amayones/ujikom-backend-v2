<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('seat_category');
        });
    }

    public function down(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->enum('category', ['regular', 'vip'])->default('regular');
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->enum('seat_category', ['regular', 'vip']);
        });
    }
};
