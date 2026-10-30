@php
	$showAccount = $showAccount ?? true;
	$inline = $inline ?? false;
@endphp

@unless($inline)
	<h2 class="ph-order-received__section-title">Bonifico anticipato</h2>
@endunless

<div class="ph-bank-message">
	{!! \App\Support\SiteTexts::html('bank.message_html') !!}
</div>

@if($showAccount && \App\Support\SiteTexts::hasBankDetails())
	<dl class="ph-bank-details">
		<div>
			<dt>Nome</dt>
			<dd>{{ \App\Support\SiteTexts::text('bank.holder') }}</dd>
		</div>
		<div>
			<dt>IBAN</dt>
			<dd class="ph-bank-details__value">{{ \App\Support\SiteTexts::text('bank.iban') }}</dd>
		</div>
		<div>
			<dt>BIC</dt>
			<dd class="ph-bank-details__value">{{ \App\Support\SiteTexts::text('bank.bic') }}</dd>
		</div>
		<div>
			<dt>Tipo di trasferimento</dt>
			<dd>{{ \App\Support\SiteTexts::text('bank.transfer_type') }}</dd>
		</div>
	</dl>
@endif
