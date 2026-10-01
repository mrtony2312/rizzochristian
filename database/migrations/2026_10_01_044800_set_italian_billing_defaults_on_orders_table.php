<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('country')->default('IT')->change();
            $table->string('payment_method')->default('bonifico')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('country')->default('DE')->change();
            $table->string('payment_method')->default('vorkasse')->change();
        });
    }
};
