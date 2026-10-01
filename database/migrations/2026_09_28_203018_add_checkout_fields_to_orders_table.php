<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('company')->nullable()->after('last_name');
            $table->string('country')->default('IT')->after('company');
            $table->string('state')->nullable()->after('city');
            $table->string('address_2')->nullable()->after('address');
            $table->string('payment_method')->default('bonifico')->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'company',
                'country',
                'state',
                'address_2',
                'payment_method',
            ]);
        });
    }
};
