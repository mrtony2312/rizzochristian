@extends('layouts.app')

@section('title', \App\Support\SiteTexts::text('confirmation.document_title').' - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta woocommerce woocommerce-order-received no-sidebar elementor-default elementor-kit-8')

@section('content')


@php
	$paymentLabel = $order->paymentLabel();
@endphp

<div id="page-header" class="page-header page-header--checkout">
	<div class="container clearfix">
		<div class="page-header__content">
			<h1 class="page-header__title">{{ \App\Support\SiteTexts::text('confirmation.page_title') }}</h1>
		</div>
	</div>
</div>

<div id="site-content" class="site-content">
	<div class="container clearfix ph-order-received">
		<p class="ph-order-received__thanks">{{ \App\Support\SiteTexts::text('confirmation.thanks') }}</p>

		@if($order->isBankTransfer())
			<div class="ph-order-received__instructions">
				@include('partials.bank-transfer-instructions', [
					'showAccount' => \App\Support\SiteTexts::text('bank.show_on_confirmation') === '1',
				])
			</div>
		@endif

		<section class="ph-order-received__details">
			<h2>{{ \App\Support\SiteTexts::text('confirmation.summary_title') }}</h2>
			<table class="shop_table order_details">
				<thead>
					<tr>
						<th class="product-name">{{ \App\Support\SiteTexts::text('confirmation.product') }}</th>
						<th class="product-total">{{ \App\Support\SiteTexts::text('confirmation.line_total') }}</th>
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
						<th>{{ \App\Support\SiteTexts::text('confirmation.subtotal') }}</th>
						<td>€{{ number_format($order->total, 2) }}</td>
					</tr>
					<tr>
						<th>{{ \App\Support\SiteTexts::text('confirmation.total') }}</th>
						<td><strong>€{{ number_format($order->total, 2) }}</strong></td>
					</tr>
					<tr>
						<th>{{ \App\Support\SiteTexts::text('confirmation.payment_method') }}</th>
						<td>{{ $order->isBankTransfer() ? \App\Support\SiteTexts::text('bank.title') : $paymentLabel }}</td>
					</tr>
					<tr>
						<th>{{ \App\Support\SiteTexts::text('confirmation.order_number') }}</th>
						<td>{{ $order->reference }}</td>
					</tr>
					<tr>
						<th>{{ \App\Support\SiteTexts::text('confirmation.date') }}</th>
						<td>{{ $order->created_at->translatedFormat('j F Y') }}</td>
					</tr>
				</tfoot>
			</table>
		</section>
	</div>
</div>
@endsection