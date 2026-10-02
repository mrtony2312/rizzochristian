@php
	$catalog = app(\App\Support\MerchantCatalog::class);
	$returns = $catalog->returns();
@endphp
<p class="ph-purchase-terms">
	{{ $catalog->purchaseTermsHtml() }}
	<a href="{{ url('/spedizione-e-consegna') }}">Spedizione e consegna</a>
	·
	<a href="{{ url('/resi-e-rimborsi') }}">Reso entro {{ $returns['days'] }} giorni</a>
	·
	<a href="{{ url('/metodi-di-pagamento') }}">Pagamento tramite bonifico anticipato</a>
</p>
