@extends('layouts.app')

@section('title', 'Carrello - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta woocommerce woocommerce-cart woocommerce-page no-sidebar elementor-default elementor-kit-8')

@section('content')
<div id="page-header" class="page-header">
	<div class="container clearfix">
		<div class="page-header__content">
			<h1 class="page-header__title">Carrello</h1>
		</div>
	</div>
</div>

<div id="site-content" class="site-content">
	<div class="container clearfix">

		@if (empty($cart))
			<p class="cart-empty woocommerce-info">Il tuo carrello è attualmente vuoto.</p>
			<p><a class="button wc-backward" href="{{ route('shop') }}">Torna al negozio</a></p>
		@else
			<form class="woocommerce-cart-form">
			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
				<thead>
					<tr>
						<th class="product-remove">&nbsp;</th>
						<th class="product-thumbnail">&nbsp;</th>
						<th class="product-name">Prodotto</th>
						<th class="product-price">Prezzo</th>
						<th class="product-quantity">Quantità</th>
						<th class="product-subtotal">Subtotale</th>
					</tr>
				</thead>
				<tbody>
					@php($total = 0)
					@foreach ($cart as $productId => $qty)
						@continue(!isset($products[$productId]))
						@php($product = $products[$productId])
						@php($lineTotal = $product->price * $qty)
						@php($total += $lineTotal)
						<tr class="woocommerce-cart-form__cart-item cart_item">
							<td class="product-remove">
								<a href="#" class="remove ajax-remove-from-cart" data-url="{{ route('cart.remove', $product) }}" aria-label="Rimuovi">&times;</a>
							</td>
							<td class="product-thumbnail">
								<a href="{{ route('product', $product->slug) }}">
									<img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" width="64" height="64">
								</a>
							</td>
							<td class="product-name" data-title="Prodotto">
								<a href="{{ route('product', $product->slug) }}">{{ $product->name }}</a>
							</td>
							<td class="product-price" data-title="Prezzo">
								<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->price, 2) }}</bdi></span>
							</td>
							<td class="product-quantity" data-title="Quantità">{{ $qty }}</td>
							<td class="product-subtotal" data-title="Subtotale">
								<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($lineTotal, 2) }}</bdi></span>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			</form>

			<div class="cart-collaterals">
				<div class="cart_totals">
					<h2>Totali carrello</h2>
					<table cellspacing="0">
						<tbody>
							<tr class="shipping">
								<th>Spedizione</th>
								<td>Gratuita in Italia <span class="woocommerce-Price-amount amount"><bdi>0,00&nbsp;€</bdi></span></td>
							</tr>
							<tr class="order-total">
								<th>Totale (IVA inclusa)</th>
								<td><strong><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($total, 2) }}</bdi></span></strong></td>
							</tr>
						</tbody>
					</table>
					@include('partials.purchase-terms')
					<div class="wc-proceed-to-checkout">
						<a class="checkout-button button alt wc-forward" href="{{ route('checkout') }}">Vai alla cassa</a>
					</div>
				</div>
			</div>
		@endif

	</div>
</div>
@endsection
