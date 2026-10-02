<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_place_a_bank_transfer_order(): void
    {
        Mail::fake();

        $product = Product::factory()->create([
            'price' => 80.50,
            'regular_price' => 80.50,
            'in_stock' => true,
        ]);

        $response = $this->withSession(['cart' => [$product->id => 2]])
            ->post(route('checkout.store'), [
                'email' => 'cliente@example.com',
                'first_name' => 'Mario',
                'last_name' => 'Rossi',
                'country' => 'IT',
                'address' => 'Via Roma 1',
                'postal_code' => '10121',
                'city' => 'Torino',
                'state' => 'TO',
                'phone' => '+393331112233',
                'payment_method' => 'bonifico',
            ]);

        $order = Order::query()->first();
        $this->assertNotNull($order);
        $this->assertSame(161.0, (float) $order->total);
        $this->assertSame('cliente@example.com', $order->email);
        $this->assertSame('pending', $order->status);
        $this->assertCount(1, $order->items);
        $this->assertSame(2, (int) $order->items->first()->quantity);

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertEmpty(session('cart', []));
    }

    public function test_checkout_page_is_compact_on_mobile_markup(): void
    {
        $product = Product::factory()->create([
            'price' => 50,
            'regular_price' => 50,
            'in_stock' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('ph-checkout__mobile-bar', false)
            ->assertSee('data-checkout-submit', false)
            ->assertSee('checkout.css', false);
    }

    public function test_validation_errors_are_shown_when_required_fields_are_missing(): void
    {
        $product = Product::factory()->create(['in_stock' => true]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->from(route('checkout'))
            ->post(route('checkout.store'), [
                'payment_method' => 'bonifico',
                'country' => 'IT',
            ])
            ->assertRedirect(route('checkout'))
            ->assertSessionHasErrors(['email', 'first_name', 'last_name', 'address', 'postal_code', 'city']);
    }
}
