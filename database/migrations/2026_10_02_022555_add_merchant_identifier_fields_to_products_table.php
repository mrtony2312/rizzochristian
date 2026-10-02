<?php

use App\Support\MerchantProductIdentifiers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('sku');
            $table->string('gtin', 14)->nullable()->after('brand');
            $table->string('mpn')->nullable()->after('gtin');
            $table->string('energy_efficiency_class', 8)->nullable()->after('mpn');
        });

        foreach (DB::table('products')->orderBy('id')->get() as $product) {
            $brand = MerchantProductIdentifiers::brandFromText($product->description, $product->name);
            $gtin = MerchantProductIdentifiers::gtinFromText($product->description, $product->short_description);
            $energy = MerchantProductIdentifiers::energyEfficiencyClassFromText(
                $product->description,
                $product->short_description
            );

            if ($brand === null && $gtin === null && $energy === null) {
                continue;
            }

            DB::table('products')->where('id', $product->id)->update([
                'brand' => $brand,
                'gtin' => $gtin,
                'energy_efficiency_class' => $energy,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand', 'gtin', 'mpn', 'energy_efficiency_class']);
        });
    }
};
