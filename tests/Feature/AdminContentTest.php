<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_customer_cannot_open_the_editor(): void
    {
        $this->get(route('admin.edit'))
            ->assertRedirect(route('admin.login'));

        $customer = User::factory()->create(['is_admin' => false]);

        $this->actingAs($customer)
            ->get(route('admin.edit'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_sign_in_and_change_the_payment_texts(): void
    {
        $this->post(route('admin.login.submit'), [
            'email' => 'admin@rizzochristian.com',
            'password' => '12345678',
        ])->assertRedirect(route('admin.edit'));

        $this->post(route('admin.update'), [
            'locale' => 'it',
            'values' => [
                ['key' => 'confirmation.thanks', 'value' => 'Grazie, ordine registrato.'],
                ['key' => 'bank.holder', 'value' => 'Nuovo Titolare'],
                ['key' => 'bank.email', 'value' => 'copia@rizzochristian.com'],
                ['key' => 'payment.exclusive', 'value' => 'Solo bonifico, testo personalizzato.'],
                ['key' => 'checkout.option_description', 'value' => 'Descrizione cassa personalizzata.'],
            ],
        ])->assertRedirect(route('admin.edit', ['locale' => 'it']));

        $order = Order::factory()->create();

        $this->get(route('checkout.success', $order))
            ->assertOk()
            ->assertSee('Grazie, ordine registrato.', false)
            ->assertSee('Paga direttamente sul nostro conto bancario.', false)
            ->assertSee('velocizzare la spedizione', false)
            ->assertDontSee('Nuovo Titolare', false)
            ->assertDontSee('IT09Q3608105138217896017932', false);

        $this->post(route('admin.update'), [
            'locale' => 'it',
            'values' => [
                ['key' => 'bank.show_on_confirmation', 'value' => '1'],
            ],
        ])->assertRedirect(route('admin.edit', ['locale' => 'it']));

        $this->get(route('checkout.success', $order))
            ->assertOk()
            ->assertSee('Nuovo Titolare', false)
            ->assertSee('copia@rizzochristian.com', false);

        $this->get('/metodi-di-pagamento')
            ->assertOk()
            ->assertSee('Solo bonifico, testo personalizzato.', false)
            ->assertSee('Nuovo Titolare', false)
            ->assertSee('copia@rizzochristian.com', false);

        $product = Product::factory()->create([
            'price' => 10,
            'regular_price' => 10,
            'in_stock' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('Descrizione cassa personalizzata.', false);

        $mail = (new OrderConfirmation($order->fresh()))->render();
        $this->assertStringContainsString('Nuovo Titolare', $mail);
        $this->assertStringContainsString('copia@rizzochristian.com', $mail);
    }

    public function test_unknown_locale_is_rejected(): void
    {
        $admin = User::query()->where('email', 'admin@rizzochristian.com')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('admin.edit'))
            ->post(route('admin.update'), [
                'locale' => 'fr',
                'values' => [
                    ['key' => 'bank.holder', 'value' => 'Altro nome'],
                ],
            ])
            ->assertRedirect(route('admin.edit'))
            ->assertSessionHasErrors('locale');
    }
}
