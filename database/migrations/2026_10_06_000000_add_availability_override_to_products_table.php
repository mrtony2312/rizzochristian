<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Allows per-product Merchant availability override:
            // null = auto (in_stock / out_of_stock from boolean),
            // or one of: limited_availability, backorder, preorder
            $table->string('availability_override')->nullable()->after('in_stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('availability_override');
        });
    }
};
