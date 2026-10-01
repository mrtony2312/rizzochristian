<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $dataPath = database_path('data');

        $this->remapSlugs($dataPath);

        $categories = json_decode(File::get($dataPath.'/categories.json'), true);
        $categoryMap = [];

        foreach ($categories as $cat) {
            $attributes = [
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'description' => $cat['description'] ?: null,
                'image' => $cat['image'],
            ];

            $model = $this->upsertByIdOrSlug(Category::class, $cat['id'] ?? null, $cat['slug'], $attributes);
            $categoryMap[$cat['slug']] = $model->id;
        }

        $products = json_decode(File::get($dataPath.'/products.json'), true);

        foreach ($products as $p) {
            $categoryId = null;
            foreach ($p['categories'] as $slug) {
                if (isset($categoryMap[$slug])) {
                    $categoryId = $categoryMap[$slug];
                    break;
                }
            }

            $attributes = [
                'category_id' => $categoryId,
                'name' => $p['name'],
                'slug' => $p['slug'],
                'sku' => $p['sku'],
                'price' => $p['price'],
                'regular_price' => $p['regular_price'],
                'short_description' => $p['short_description'],
                'description' => $p['description'],
                'attributes' => $p['attributes'],
                'image' => $p['images'][0] ?? null,
                'in_stock' => $p['in_stock'] ?? true,
            ];

            $product = $this->upsertByIdOrSlug(Product::class, $p['id'] ?? null, $p['slug'], $attributes);

            $product->images()->delete();
            foreach ($p['images'] as $i => $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $image,
                    'sort_order' => $i,
                ]);
            }
        }
    }

    /**
     * Prefer matching by JSON id; if missing, match by slug (avoids unique
     * conflicts after one-shot slug remaps on rows with different ids).
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $attributes
     */
    private function upsertByIdOrSlug(string $modelClass, ?int $id, string $slug, array $attributes): Category|Product
    {
        $model = null;

        if ($id !== null) {
            $model = $modelClass::query()->find($id);
        }

        if ($model === null) {
            $model = $modelClass::query()->where('slug', $slug)->first();
        }

        if ($model !== null) {
            // Another row may already own this slug after remap; clear it first.
            $modelClass::query()
                ->where('slug', $slug)
                ->where('id', '!=', $model->id)
                ->update(['slug' => $slug.'-legacy-'.$model->id]);

            $model->fill($attributes)->save();

            return $model;
        }

        if ($id !== null) {
            $modelClass::query()
                ->where('slug', $slug)
                ->update(['slug' => $slug.'-legacy-'.$id]);

            return $modelClass::query()->create(array_merge(['id' => $id], $attributes));
        }

        return $modelClass::query()->create($attributes);
    }

    private function remapSlugs(string $dataPath): void
    {
        $categoryMapPath = $dataPath.'/category-slug-map.json';
        if (File::exists($categoryMapPath)) {
            $categorySlugMap = json_decode(File::get($categoryMapPath), true) ?: [];
            foreach ($categorySlugMap as $oldSlug => $newSlug) {
                Category::where('slug', $oldSlug)->update(['slug' => $newSlug]);
            }
        }

        $productMapPath = $dataPath.'/product-slug-map.json';
        if (File::exists($productMapPath)) {
            $productSlugMap = json_decode(File::get($productMapPath), true) ?: [];
            foreach ($productSlugMap as $oldSlug => $newSlug) {
                Product::where('slug', $oldSlug)->update(['slug' => $newSlug]);
            }
        }
    }
}
