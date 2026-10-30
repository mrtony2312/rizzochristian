@php
	$bankTitle = \App\Support\SiteTexts::text('bank.title');
	$bankIntro = \App\Support\SiteTexts::text('bank.intro');
	$holderLabel = \App\Support\SiteTexts::text('bank.holder_label');
	$holder = \App\Support\SiteTexts::text('bank.holder');
	$ibanLabel = \App\Support\SiteTexts::text('bank.iban_label');
	$iban = \App\Support\SiteTexts::text('bank.iban');
	$bicLabel = \App\Support\SiteTexts::text('bank.bic_label');
	$bic = \App\Support\SiteTexts::text('bank.bic');
	$transferLabel = \App\Support\SiteTexts::text('bank.transfer_type_label');
	$transferType = \App\Support\SiteTexts::text('bank.transfer_type');
	$receipt = \App\Support\SiteTexts::text('bank.receipt');
	$paymentEmail = \App\Support\SiteTexts::text('bank.email');
	$whatsappNumber = preg_replace('/\D+/', '', (string) config('services.whatsapp.number', ''));
	$whatsappUrl = $whatsappNumber !== '' ? 'https://wa.me/'.$whatsappNumber : null;
	$whatsappSentence = e(\App\Support\SiteTexts::text('bank.whatsapp'));
	if ($whatsappUrl && str_contains($whatsappSentence, 'WhatsApp')) {
		$whatsappSentence = str_replace(
			'WhatsApp',
			'<a href="'.e($whatsappUrl).'" target="_blank" rel="noopener noreferrer">WhatsApp</a>',
			$whatsappSentence
		);
	}
	$inline = $inline ?? false;
	$showAccount = $showAccount ?? true;
@endphp

@if($inline)
	<p>{{ $bankIntro }}</p>
	@if($showAccount)
		<p>
			<strong>{{ $holderLabel }}:</strong> {{ $holder }}<br>
			<strong>{{ $ibanLabel }}:</strong> {{ $iban }}<br>
			<strong>{{ $bicLabel }}:</strong> {{ $bic }}<br>
			<strong>{{ $transferLabel }}:</strong> {{ $transferType }}
		</p>
	@endif
	<p>{!! $whatsappSentence !!}</p>
	<p>{{ $receipt }} <a href="mailto:{{ $paymentEmail }}">{{ $paymentEmail }}</a>.</p>
@else
	<h2 class="ph-order-received__section-title">{{ $bankTitle }}</h2>
	<p>{{ $bankIntro }}</p>

	@if($showAccount)
	<dl class="ph-bank-details">
		<div>
			<dt>{{ $holderLabel }}</dt>
			<dd>{{ $holder }}</dd>
		</div>
		<div>
			<dt>{{ $ibanLabel }}</dt>
			<dd class="ph-bank-details__value">{{ $iban }}</dd>
		</div>
		<div>
			<dt>{{ $bicLabel }}</dt>
			<dd class="ph-bank-details__value">{{ $bic }}</dd>
		</div>
		<div>
			<dt>{{ $transferLabel }}</dt>
			<dd>{{ $transferType }}</dd>
		</div>
	</dl>
	@endif

	<p>{!! $whatsappSentence !!}</p>
	<p>{{ $receipt }} <a href="mailto:{{ $paymentEmail }}">{{ $paymentEmail }}</a>.</p>
@endif
