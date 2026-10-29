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

		@if($order->isBankTransfer())
			<div class="ph-order-received__instructions">
				<h2 class="ph-order-received__section-title">Bonifico anticipato</h2>
				<p>Paga direttamente sul nostro conto bancario. L'ordine verrà spedito solo dopo la ricezione del pagamento.</p>

				<p>Dopo aver effettuato il bonifico, puoi inviarci la ricevuta via WhatsApp per velocizzare la spedizione.</p>
				<p>Ti preghiamo di inviarci una copia del bonifico a
					<a href="mailto:contatto@rizzochristian.com">contatto@rizzochristian.com</a>.
				</p>
			</div>
		@endif

		<section class="ph-order-received__details">
			<h2>Riepilogo ordine</h2>
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
					<tr>
						<th>Numero ordine:</th>
						<td>{{ $order->reference }}</td>
					</tr>
					<tr>
						<th>Data:</th>
						<td>{{ $order->created_at->translatedFormat('j F Y') }}</td>
					</tr>
				</tfoot>
			</table>
		</section>
	</div>
</div>
@endsection