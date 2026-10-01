<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogCategoryFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_page_preserves_price_filter_when_switching_category_links(): void
    {
        $brennholz = Category::factory()->create(['name' => 'Legna da ardere', 'slug' => 'legna-da-ardere']);
        $pellets = Category::factory()->create(['name' => 'Pellet di legno', 'slug' => 'pellet-di-legno']);

        Product::factory()->create(['category_id' => $brennholz->id, 'price' => 80, 'name' => 'Buche cheap']);
        Product::factory()->create(['category_id' => $pellets->id, 'price' => 90, 'name' => 'Pellets cheap']);

        $response = $this->get(route('category', [
            'slug' => 'legna-da-ardere',
            'price_range' => ['0-100'],
        ]));

        $response->assertOk();
        $response->assertSee('Affina per', false);
        $response->assertSee('Legna da ardere', false);
        $response->assertSee('page-header__image', false);
        $response->assertSee('categoria-prodotto/pellet-di-legno', false);
        $response->assertSee('price_range', false);
    }

    public function test_shop_keeps_price_and_category_filters_together(): void
    {
        $brennholz = Category::factory()->create(['name' => 'Legna da ardere', 'slug' => 'legna-da-ardere']);
        Product::factory()->create(['category_id' => $brennholz->id, 'price' => 80, 'name' => 'Buche cheap']);
        Product::factory()->create(['category_id' => $brennholz->id, 'price' => 300, 'name' => 'Buche expensive']);

        $response = $this->get(route('shop', [
            'product_cat' => ['legna-da-ardere'],
            'price_range' => ['0-100'],
        ]));

        $response->assertOk();
        $response->assertSee('Buche cheap', false);
        $response->assertDontSee('Buche expensive', false);
        $response->assertSee('page-header__image', false);
    }
}
