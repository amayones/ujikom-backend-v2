<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->index('show_time');
            $table->index(['studio_id', 'show_time']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('payment_status');
            $table->index('order_number');
            $table->index('created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('schedule_id');
            $table->index('seat_id');
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->index(['studio_id', 'row', 'column']);
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex(['show_time']);
            $table->dropIndex(['studio_id', 'show_time']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['order_number']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['schedule_id']);
            $table->dropIndex(['seat_id']);
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->dropIndex(['studio_id', 'row', 'column']);
        });
    }
};
