<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantCenterComplianceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_exposes_price_shipping_and_return_data(): void
    {
        $category = Category::factory()->create([
            'name' => 'Pellet di legno',
            'slug' => 'pellet-di-legno',
        ]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Pfeifer Pellet sacco 15 kg',
            'slug' => 'pfeifer-pellet-sacco',
            'price' => 245.00,
            'regular_price' => 299.00,
            'description' => "Marca: Pfeifer\nEAN: 4006381333931",
            'in_stock' => true,
        ]);

        $response = $this->get(route('product', $product->slug));

        $response->assertOk();
        $response->assertSee('Spedizione gratuita in tutta l’Italia', false);
        $response->assertSee('1–2 giorni lavorativi', false);
        $response->assertSee('2–4 giorni lavorativi', false);
        $response->assertDontSee('7–10 giorni', false);
        $response->assertSee('IVA inclusa', false);
        $response->assertSee('Reso entro 14 giorni', false);
        $response->assertSee('schema.org/InStock', false);
        $response->assertSee('4006381333931', false);
        $response->assertSee('Pfeifer', false);
        $response->assertDontSee('See Details', false);
        $response->assertDontSee('Delivery within 3-5', false);
        $response->assertDontSee('resi gratuiti', false);
    }

    public function test_out_of_stock_product_cannot_be_added_to_the_cart(): void
    {
        $product = Product::factory()->create([
            'slug' => 'esaurito-pellet',
            'in_stock' => false,
        ]);

        $this->get(route('product', $product->slug))
            ->assertOk()
            ->assertSee('Esaurito', false)
            ->assertSee('schema.org/OutOfStock', false);

        $this->post(route('cart.add', $product))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertEmpty(session('cart', []));
    }

    public function test_payment_and_return_pages_match_the_checkout(): void
    {
        $this->get('/metodi-di-pagamento/')
            ->assertOk()
            ->assertSee('bonifico anticipato', false)
            ->assertDontSee('Carta di credito', false);

        $this->get('/resi-e-rimborsi/')
            ->assertOk()
            ->assertSee('14 giorni', false)
            ->assertSee('spese di restituzione sono a carico del cliente', false);

        $this->get('/spedizione-e-consegna/')
            ->assertOk()
            ->assertSee('1–2 giorni lavorativi', false)
            ->assertSee('2–4 giorni lavorativi', false)
            ->assertSee('gratuita in tutta l’Italia', false)
            ->assertDontSee('7–10 giorni', false);
    }

    public function test_google_merchant_feed_lists_priced_products_with_free_italian_shipping(): void
    {
        $image = 'wp-content/uploads/2026/08/s-l1600-1.webp';
        if (! is_file(public_path($image))) {
            $this->markTestSkipped('Product image fixture is not present.');
        }

        $category = Category::factory()->create(['slug' => 'stufe-a-pellet', 'name' => 'Stufe a pellet']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'MCZ Stream stufa a pellet',
            'slug' => 'mcz-stream',
            'brand' => 'MCZ',
            'price' => 1200,
            'regular_price' => 1200,
            'image' => $image,
            'description' => 'Classe di efficienza energetica: A+',
            'in_stock' => true,
        ]);

        $response = $this->get(route('merchant.feed'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<g:id>PC-'.$product->id.'</g:id>', false);
        $response->assertSee('<g:brand>MCZ</g:brand>', false);
        $response->assertSee('<g:price>1200.00 EUR</g:price>', false);
        $response->assertDontSee('<g:sale_price>', false);
        $response->assertDontSee('<g:identifier_exists>no</g:identifier_exists>', false);
        $response->assertSee('<g:google_product_category>2639</g:google_product_category>', false);
        $response->assertSee('<g:country>IT</g:country>', false);
        $response->assertSee('<g:price>0.00 EUR</g:price>', false);
        $response->assertSee('<g:min_handling_time>1</g:min_handling_time>', false);
        $response->assertSee('<g:max_handling_time>2</g:max_handling_time>', false);
        $response->assertSee('<g:min_transit_time>1</g:min_transit_time>', false);
        $response->assertSee('<g:max_transit_time>2</g:max_transit_time>', false);
        $response->assertSee('<g:energy_efficiency_class>A+</g:energy_efficiency_class>', false);
        $response->assertDontSee('<g:mpn>', false);
        $response->assertDontSee('<g:brand>Rizzo Christian</g:brand>', false);

        $this->get(route('product', $product->slug))
            ->assertOk()
            ->assertSee('Classe di efficienza energetica: A+', false)
            ->assertSee('EUEnergyEfficiencyCategoryA1Plus', false);
    }

    public function test_feed_omits_brand_fallback_and_uses_identifier_exists_only_without_brand(): void
    {
        $image = 'wp-content/uploads/2026/08/s-l1600-1.webp';
        if (! is_file(public_path($image))) {
            $this->markTestSkipped('Product image fixture is not present.');
        }

        Product::factory()->create([
            'name' => 'Legna da ardere di faggio 50 cm',
            'slug' => 'legna-faggio-test',
            'brand' => null,
            'price' => 100,
            'regular_price' => 100,
            'image' => $image,
            'in_stock' => true,
        ]);

        $this->get(route('merchant.feed'))
            ->assertOk()
            ->assertSee('<g:identifier_exists>no</g:identifier_exists>', false)
            ->assertDontSee('<g:brand>Rizzo Christian</g:brand>', false)
            ->assertDontSee('<g:brand>Boutique</g:brand>', false);
    }

    public function test_old_primex_weight_slug_redirects_to_the_corrected_product(): void
    {
        $this->get('/prodotto/pellet-di-qualita-primex-premium-990-kg/')
            ->assertStatus(301)
            ->assertRedirect(route('product', 'pellet-di-qualita-primex-premium-975-kg'));
    }

    public function test_home_footer_links_policies_and_embeds_the_logo(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('images/logo-rizzo.png', false)
            ->assertSee('clamp(52px, 7.5vw, 88px)', false)
            ->assertSee('Le nostre politiche', false)
            ->assertSee('/spedizione-e-consegna/', false)
            ->assertDontSee('Unsere Richtlinien', false)
            ->assertDontSee('Garanzia sul prezzo', false);
    }

    public function test_checkout_shows_the_same_total_as_the_product_price(): void
    {
        $product = Product::factory()->create([
            'price' => 80,
            'regular_price' => 80,
            'in_stock' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 2]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('Spedizione in Italia', false)
            ->assertSee('Gratuita', false)
            ->assertSee('Totale IVA inclusa', false)
            ->assertSee('€160.00', false)
            ->assertDontSee('coupon', false);
    }
}
