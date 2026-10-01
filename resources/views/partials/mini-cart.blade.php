@php
    $cart = session('cart', []);
    $items = \App\Models\Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
    $total = 0;
    $itemCount = 0;
    foreach ($cart as $productId => $qty) {
        if (! isset($items[$productId])) {
            continue;
        }
        $total += $items[$productId]->price * $qty;
        $itemCount += $qty;
    }
@endphp
@if (empty($cart) || $items->isEmpty())
	<p class="woocommerce-mini-cart__empty-message woocommerce-mini-cart__empty--panel">
		<img src="{{ asset('wp-content/themes/motta/images/empty-bag.svg') }}" alt="Nessun prodotto nel carrello.">
		Nessun prodotto nel carrello.
	</p>
@else
	<ul class="woocommerce-mini-cart cart_list product_list_widget">
		@foreach ($cart as $productId => $qty)
			@continue(!isset($items[$productId]))
			@php($product = $items[$productId])
			<li class="woocommerce-mini-cart-item mini_cart_item">
				<div class="woocommerce-mini-cart-item__thumbnail">
					<a href="{{ route('product', $product->slug) }}">
						<img width="88" height="88" src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy">
					</a>
				</div>
				<div class="woocommerce-mini-cart-item__summary">
					<div class="woocommerce-mini-cart-item__box">
						<div class="woocommerce-mini-cart-item__data">
							<a class="woocommerce-mini-cart-item__name" href="{{ route('product', $product->slug) }}">{{ $product->name }}</a>
							<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->price, 2) }}</bdi></span>
							<div class="woocommerce-mini-cart-item__qty">
								<div class="quantity cart-panel-qty" data-update-url="{{ route('cart.update', $product) }}">
									<button type="button" class="qty-btn qty-btn--minus" aria-label="Diminuisci quantità">&minus;</button>
									<input type="number" class="input-text qty text ajax-cart-qty" value="{{ $qty }}" min="1" step="1" inputmode="numeric" aria-label="Quantità">
									<button type="button" class="qty-btn qty-btn--plus" aria-label="Aumenta quantità">&plus;</button>
								</div>
							</div>
						</div>
					</div>
					<div class="woocommerce-mini-cart-item__remove">
						<a href="#" class="remove remove_from_cart_button ajax-remove-from-cart" data-url="{{ route('cart.remove', $product) }}" aria-label="Rimuovi" title="Rimuovi">
							<span class="motta-svg-icon motta-svg-icon--trash" aria-hidden="true">
								<svg width="18" height="18" viewBox="0 0 32 32" focusable="false"><path d="M12 4h8v2.4h6.4v3.2h-20.8v-3.2h6.4v-2.4zM8.8 12.8h14.4v14.4c0 0.88-0.72 1.6-1.6 1.6h-11.2c-0.88 0-1.6-0.72-1.6-1.6v-14.4zM12 16v8h2.4v-8h-2.4zM17.6 16v8h2.4v-8h-2.4z"></path></svg>
							</span>
						</a>
					</div>
				</div>
			</li>
		@endforeach
	</ul>
	<div class="widget_shopping_cart_footer">
		<p class="woocommerce-mini-cart__total total">
			<strong>Subtotale ({{ $itemCount }} {{ $itemCount === 1 ? 'articolo' : 'articoli' }})</strong>
			<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($total, 2) }}</bdi></span>
		</p>
		<p class="woocommerce-mini-cart__buttons buttons">
			<a href="{{ route('checkout') }}" class="checkout wc-forward cart-panel-checkout">Cassa</a>
			<a href="{{ route('cart') }}" class="viewcart view-cart cart-panel-viewcart">Vedi carrello</a>
		</p>
	</div>
@endif
