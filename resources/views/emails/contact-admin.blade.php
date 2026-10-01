<x-mail::message>
# Nuova richiesta di contatto

Hai ricevuto un nuovo messaggio tramite il modulo di contatto su **Rizzo Christian**.

<x-mail::panel>
**Nome:** {{ $contact['name'] }}  
**E-mail:** [{{ $contact['email'] }}](mailto:{{ $contact['email'] }})  
**Oggetto:** {{ filled($contact['subject'] ?? null) ? $contact['subject'] : '—' }}
</x-mail::panel>

## Messaggio

{{ $contact['message'] }}

<x-mail::button :url="'mailto:'.$contact['email']" color="primary">
Rispondi
</x-mail::button>

Cordiali saluti,<br>
Sistema Rizzo Christian
</x-mail::message>
