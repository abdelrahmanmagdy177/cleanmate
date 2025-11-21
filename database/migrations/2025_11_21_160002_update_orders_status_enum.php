<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            Schema::table('orders', function (Blueprint $table) {
                $table->string('status')->default('pending');
            });
        } else {
            // We are using DB statement because changing enum columns is not directly supported by Schema builder in all drivers without doctrine/dbal.
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'assigned', 'worker_on_way', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            Schema::table('orders', function (Blueprint $table) {
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            });
        } else {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
