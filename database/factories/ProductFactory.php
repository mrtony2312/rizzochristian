<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(Str::random(8)),
            'price' => fake()->randomFloat(2, 50, 400),
            'regular_price' => fake()->randomFloat(2, 50, 500),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'image' => null,
            'in_stock' => true,
        ];
    }
}
