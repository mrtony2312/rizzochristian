@php
	$bank = config('billing.bank_transfer');
	$whatsappNumber = preg_replace('/\D+/', '', (string) config('services.whatsapp.number', ''));
	$whatsappUrl = $whatsappNumber !== '' ? 'https://wa.me/'.$whatsappNumber : null;
	$paymentEmail = config('mail.admin_address');
@endphp

<h2 class="ph-order-received__section-title">Bonifico anticipato</h2>
<p>Paga direttamente sul nostro conto bancario. L'ordine verrà spedito solo dopo la ricezione del pagamento.</p>

<dl class="ph-bank-details">
	<div>
		<dt>Nome</dt>
		<dd>{{ $bank['holder'] }}</dd>
	</div>
	<div>
		<dt>IBAN</dt>
		<dd class="ph-bank-details__value">{{ $bank['iban'] }}</dd>
	</div>
	<div>
		<dt>BIC</dt>
		<dd class="ph-bank-details__value">{{ $bank['bic'] }}</dd>
	</div>
	<div>
		<dt>Tipo di trasferimento</dt>
		<dd>{{ $bank['transfer_type'] }}</dd>
	</div>
</dl>

<p>Dopo aver effettuato il bonifico, puoi inviarci la ricevuta via
	@if($whatsappUrl)
		<a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>
	@else
		WhatsApp
	@endif
	per velocizzare la spedizione.
</p>
<p>Ti preghiamo di inviarci una copia del bonifico a
	<a href="mailto:{{ $paymentEmail }}">{{ $paymentEmail }}</a>.
</p>
