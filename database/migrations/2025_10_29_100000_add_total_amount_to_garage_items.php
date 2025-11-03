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
        Schema::table('garage_items', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->after('price')->default(0.00);
            $table->decimal('avg_price', 10, 2)->after('price')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('garage_items', function (Blueprint $table) {
            $table->dropColumn('total_amount');
            $table->dropColumn('avg_price');
        });
    }
};
