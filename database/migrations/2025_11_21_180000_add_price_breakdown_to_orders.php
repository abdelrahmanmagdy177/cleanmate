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
            $table->decimal('service_price', 10, 2)->after('total_price')->default(0);
            $table->decimal('delivery_fee', 10, 2)->after('service_price')->default(0);
            $table->decimal('vat_rate', 5, 2)->after('delivery_fee')->default(14.00);
            $table->decimal('vat_amount', 10, 2)->after('vat_rate')->default(0);
            // total_price already exists, will be recalculated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['service_price', 'delivery_fee', 'vat_rate', 'vat_amount']);
        });
    }
};
