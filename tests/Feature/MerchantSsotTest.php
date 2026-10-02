<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Support\MerchantCatalog;
use App\Support\MerchantListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantSsotTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipping_and_returns_come_from_merchant_config(): void
    {
        $catalog = app(MerchantCatalog::class);
        $shipping = $catalog->shipping();
        $returns = $catalog->returns();

        $this->assertSame('IT', $shipping['country']);
        $this->assertSame('0.00', $shipping['price']);
        $this->assertSame(1, $shipping['handling_min']);
        $this->assertSame(2, $shipping['handling_max']);
        $this->assertSame(14, $returns['days']);

        $category = Category::factory()->create(['slug' => 'pellet-di-legno']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand' => 'Pfeifer',
            'gtin' => '4006381333931',
            'price' => 100,
            'regular_price' => 100,
            'in_stock' => true,
        ]);

        $listing = new MerchantListing($product);
        $schema = $listing->schema();

        $this->assertSame('Pfeifer', $schema['brand']['name']);
        $this->assertSame('4006381333931', $schema['gtin']);
        $this->assertSame(
            config('merchant.shipping.min_handling_time'),
            $schema['offers']['shippingDetails']['deliveryTime']['handlingTime']['minValue']
        );
        $this->assertSame(
            config('merchant.returns.days'),
            $schema['offers']['hasMerchantReturnPolicy']['merchantReturnDays']
        );
    }

    public function test_sitemap_lists_products_and_robots_points_to_it(): void
    {
        $product = Product::factory()->create(['slug' => 'pellet-test-ssot']);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('product', $product->slug), false)
            ->assertSee(route('merchant.feed'), false);

        $robots = file_get_contents(public_path('robots.txt'));
        $this->assertStringContainsString('Sitemap:', $robots);
        $this->assertStringContainsString('sitemap.xml', $robots);
    }

    public function test_feed_and_product_page_share_config_shipping_copy(): void
    {
        $image = 'wp-content/uploads/2026/08/s-l1600-1.webp';
        if (! is_file(public_path($image))) {
            $this->markTestSkipped('Product image fixture is not present.');
        }

        $product = Product::factory()->create([
            'slug' => 'ssot-pellet',
            'image' => $image,
            'price' => 90,
            'regular_price' => 90,
            'brand' => 'Pfeifer',
            'in_stock' => true,
        ]);

        $this->get(route('merchant.feed'))
            ->assertOk()
            ->assertSee('<g:min_handling_time>'.config('merchant.shipping.min_handling_time').'</g:min_handling_time>', false)
            ->assertSee('<g:brand>Pfeifer</g:brand>', false);

        $this->get(route('product', $product->slug))
            ->assertOk()
            ->assertSee('1–2 giorni lavorativi', false)
            ->assertSee('2–4 giorni lavorativi', false);
    }
}
