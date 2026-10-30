<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Order;
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
            'password' => 'admin@23',
        ])->assertRedirect(route('admin.edit'));

        $this->get(route('admin.edit'))
            ->assertOk()
            ->assertSee('Message de paiement', false)
            ->assertSee('Texte complet', false)
            ->assertDontSee('Langue affichée', false)
            ->assertDontSee('Titre du récapitulatif', false)
            ->assertDontSee('Colonne produit', false);

        $this->post(route('admin.update'), [
            'locale' => 'it',
            'values' => [
                ['key' => 'bank.message_html', 'value' => '<script>alert(1)</script><p>Pagamento personalizzato.</p>'],
                ['key' => 'bank.holder', 'value' => 'Nuovo Titolare'],
            ],
        ])->assertRedirect(route('admin.edit', ['locale' => 'it']));

        $order = Order::factory()->create();

        $this->get(route('checkout.success', $order))
            ->assertOk()
            ->assertSee('Grazie. Il tuo ordine è stato ricevuto.', false)
            ->assertSee('Riepilogo ordine', false)
            ->assertSee('Pagamento personalizzato.', false)
            ->assertDontSee('alert(1)', false)
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
            ->assertSee('IT09Q3608105138217896017932', false);

        $this->get('/metodi-di-pagamento')
            ->assertOk()
            ->assertSee('Pagamento personalizzato.', false)
            ->assertSee('Nuovo Titolare', false);

        $mail = (new OrderConfirmation($order->fresh()))->render();
        $this->assertStringContainsString('Nuovo Titolare', $mail);
        $this->assertStringContainsString('Pagamento personalizzato.', $mail);
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
