<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check and add indexes only if they don't exist
        $this->addIndexIfNotExists('schedules', 'schedules_show_time_index', ['show_time']);
        $this->addIndexIfNotExists('schedules', 'schedules_studio_id_show_time_index', ['studio_id', 'show_time']);
        
        $this->addIndexIfNotExists('orders', 'orders_payment_status_index', ['payment_status']);
        $this->addIndexIfNotExists('orders', 'orders_order_number_index', ['order_number']);
        $this->addIndexIfNotExists('orders', 'orders_created_at_index', ['created_at']);
        
        $this->addIndexIfNotExists('order_items', 'order_items_seat_id_index', ['seat_id']);
        
        $this->addIndexIfNotExists('seats', 'seats_studio_id_row_column_index', ['studio_id', 'row', 'column']);
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('schedules_show_time_index');
            $table->dropIndex('schedules_studio_id_show_time_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_payment_status_index');
            $table->dropIndex('orders_order_number_index');
            $table->dropIndex('orders_created_at_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_seat_id_index');
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->dropIndex('seats_studio_id_row_column_index');
        });
    }
    
    private function addIndexIfNotExists(string $table, string $indexName, array $columns): void
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        
        if (empty($indexes)) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
                $blueprint->index($columns, $indexName);
            });
        }
    }
};
