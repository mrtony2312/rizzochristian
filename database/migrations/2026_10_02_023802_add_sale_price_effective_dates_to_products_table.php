<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('sale_price_starts_at')->nullable()->after('regular_price');
            $table->timestamp('sale_price_ends_at')->nullable()->after('sale_price_starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sale_price_starts_at', 'sale_price_ends_at']);
        });
    }
};
