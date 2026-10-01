@extends('layouts.app')

@section('title', 'Ordine confermato - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta woocommerce woocommerce-order-received no-sidebar elementor-default elementor-kit-8')

@section('content')
@php
	$paymentLabel = $order->paymentLabel();
@endphp

<div id="page-header" class="page-header page-header--checkout">
	<div class="container clearfix">
		<div class="page-header__content">
			<h1 class="page-header__title">Cassa</h1>
		</div>
	</div>
</div>

<div id="site-content" class="site-content">
	<div class="container clearfix ph-order-received">
		<p class="ph-order-received__thanks">Grazie. Il tuo ordine è stato ricevuto.</p>

		<ul class="ph-order-received__overview woocommerce-order-overview">
			<li>
				<span class="ph-order-received__label">Numero ordine:</span>
				<strong>{{ $order->reference }}</strong>
			</li>
			<li>
				<span class="ph-order-received__label">Data:</span>
				<strong>{{ $order->created_at->translatedFormat('j F Y') }}</strong>
			</li>
			<li>
				<span class="ph-order-received__label">Totale:</span>
				<strong>€{{ number_format($order->total, 2) }}</strong>
			</li>
			<li>
				<span class="ph-order-received__label">Metodo di pagamento:</span>
				<strong>{{ $paymentLabel }}</strong>
			</li>
		</ul>

		@if($order->isBankTransfer())
			<div class="ph-order-received__instructions">
				<p>Grazie per il tuo ordine. Non appena il pagamento sarà accreditato, l’ordine verrà riservato e spedito.</p>
				<p>Ti preghiamo di inviarci una copia del bonifico a <a href="mailto:{{ config('mail.admin_address') }}">{{ config('mail.admin_address') }}</a>.</p>
				<p>Importante: assicurati che nome e indirizzo di consegna sul bonifico corrispondano ai dati del tuo ordine, affinché la banca non annulli il pagamento.</p>
			</div>
		@endif

		<section class="ph-order-received__details">
			<h2>Dettagli ordine</h2>
			<table class="shop_table order_details">
				<thead>
					<tr>
						<th class="product-name">Prodotto</th>
						<th class="product-total">Totale</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($order->items as $item)
						<tr>
							<td class="product-name">{{ $item->product_name }} <strong class="product-quantity">×&nbsp;{{ $item->quantity }}</strong></td>
							<td class="product-total">€{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
						</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<th>Subtotale:</th>
						<td>€{{ number_format($order->total, 2) }}</td>
					</tr>
					<tr>
						<th>Totale:</th>
						<td><strong>€{{ number_format($order->total, 2) }}</strong></td>
					</tr>
					<tr>
						<th>Metodo di pagamento:</th>
						<td>{{ $paymentLabel }}</td>
					</tr>
					@if($order->notes)
						<tr>
							<th>Nota:</th>
							<td>{{ $order->notes }}</td>
						</tr>
					@endif
					<tr>
						<th>Azioni:</th>
						<td>
							<a class="ph-order-received__invoice" href="mailto:{{ config('mail.admin_address') }}?subject=Fattura%20{{ $order->reference }}">Fattura</a>
						</td>
					</tr>
				</tfoot>
			</table>
		</section>

		<section class="ph-order-received__address">
			<h2>Indirizzo di fatturazione</h2>
			<address>
				@if($order->company){{ $order->company }}<br>@endif
				{{ $order->name }}<br>
				{{ $order->address }}@if($order->address_2), {{ $order->address_2 }}@endif<br>
				{{ $order->postal_code }} {{ $order->city }}@if($order->provinceName()), {{ $order->provinceName() }}@endif<br>
				{{ $order->countryName() }}<br>
				@if($order->phone){{ $order->phone }}<br>@endif
				{{ $order->email }}
			</address>
		</section>
	</div>
</div>
@endsection
