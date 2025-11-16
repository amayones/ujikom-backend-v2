<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('ticket_status', ['unused', 'scanned'])->default('unused')->after('payment_status');
            $table->timestamp('scanned_at')->nullable()->after('ticket_status');
            $table->unsignedBigInteger('scanned_by')->nullable()->after('scanned_at');
            $table->foreign('scanned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['scanned_by']);
            $table->dropColumn(['ticket_status', 'scanned_at', 'scanned_by']);
        });
    }
};
