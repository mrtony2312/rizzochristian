<x-mail::message>
# Grazie, {{ $contact['name'] }}!

abbiamo ricevuto il tuo messaggio e ti risponderemo il prima possibile.

<x-mail::panel>
**I tuoi dati**  
**E-mail:** {{ $contact['email'] }}  
**Oggetto:** {{ filled($contact['subject'] ?? null) ? $contact['subject'] : '—' }}
</x-mail::panel>

## Il tuo messaggio

{{ $contact['message'] }}

Per questioni urgenti puoi scriverci a [{{ config('mail.admin_address') }}](mailto:{{ config('mail.admin_address') }}).

<x-mail::button :url="route('shop')" color="primary">
Vai al negozio
</x-mail::button>

Cordiali saluti,<br>
Il team Rizzo Christian
</x-mail::message>
