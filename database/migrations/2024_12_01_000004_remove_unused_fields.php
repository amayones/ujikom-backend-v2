<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->enum('type', ['regular', 'vip'])->default('regular');
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->enum('status', ['available', 'booked'])->default('available');
        });
    }
};
