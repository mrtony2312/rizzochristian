<?php

namespace Tests\Feature;

use App\Mail\ContactAdminNotification;
use App\Mail\ContactConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_sends_admin_and_confirmation_mails(): void
    {
        Mail::fake();

        $payload = [
            'name' => 'Max Mustermann',
            'email' => 'max@example.com',
            'subject' => 'Frage zu Pellets',
            'message' => 'Hallo, ich habe eine Frage zur Lieferung.',
        ];

        $this->from(route('contact'))
            ->post(route('contact.store'), $payload)
            ->assertRedirect(route('contact'))
            ->assertSessionHas('success');

        Mail::assertSent(ContactAdminNotification::class, function (ContactAdminNotification $mail) use ($payload): bool {
            return $mail->hasTo(config('mail.admin_address'))
                && $mail->hasReplyTo($payload['email'], $payload['name'])
                && $mail->contact['name'] === $payload['name']
                && $mail->contact['message'] === $payload['message'];
        });

        Mail::assertSent(ContactConfirmation::class, function (ContactConfirmation $mail) use ($payload): bool {
            return $mail->hasTo($payload['email'])
                && $mail->contact['subject'] === $payload['subject'];
        });
    }

    public function test_contact_form_requires_name_email_and_message(): void
    {
        Mail::fake();

        $this->from(route('contact'))
            ->post(route('contact.store'), [])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors(['name', 'email', 'message']);

        Mail::assertNothingSent();
    }

    public function test_contact_confirmation_mail_contains_branded_content(): void
    {
        $mailable = new ContactConfirmation([
            'name' => 'Anna',
            'email' => 'anna@example.com',
            'subject' => 'Beratung',
            'message' => 'Können Sie mich beraten?',
        ]);

        $mailable->assertHasSubject('Abbiamo ricevuto il tuo messaggio – Rizzo Christian');
        $mailable->assertSeeInHtml('Grazie, Anna!');
        $mailable->assertSeeInHtml('Können Sie mich beraten?');
        $mailable->assertSeeInText('Rizzo Christian');
    }

    public function test_contact_admin_mail_contains_message_details(): void
    {
        $mailable = new ContactAdminNotification([
            'name' => 'Anna',
            'email' => 'anna@example.com',
            'subject' => 'Beratung',
            'message' => 'Können Sie mich beraten?',
        ]);

        $mailable->assertHasSubject('[Contatto] Beratung');
        $mailable->assertSeeInHtml('Nuova richiesta di contatto');
        $mailable->assertSeeInHtml('anna@example.com');
        $mailable->assertSeeInHtml('Können Sie mich beraten?');
    }
}
