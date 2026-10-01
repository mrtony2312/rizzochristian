<div class="motta-product-gallery-quickview">
	<img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" width="400" height="400" loading="lazy">
</div>
<div class="summary entry-summary">
	<h2 class="product_title">{{ $product->name }}</h2>
	@if($product->category)
		<div class="meta meta-cat">in <a href="{{ route('category', $product->category->slug) }}">{{ $product->category->name }}</a></div>
	@endif
	<p class="price">
		@if($product->isOnSale())
			<ins><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->price, 2) }}</bdi></span></ins>
			<del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->regular_price, 2) }}</bdi></span></del>
		@else
			<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->price, 2) }}</bdi></span>
		@endif
	</p>
	<div class="woocommerce-product-details__short-description">
		<p>{{ \Illuminate\Support\Str::limit($product->short_description, 220) }}</p>
	</div>
	<form class="cart ajax-cart-form ph-product-cart" action="{{ route('cart.add', $product) }}" method="post">
		@csrf
		<div class="quantity motta-qty-stepper">
			<button type="button" class="qty-btn qty-btn--minus" aria-label="Diminuisci quantità">&minus;</button>
			<input type="number" name="quantity" value="1" min="1" class="input-text qty text" inputmode="numeric">
			<button type="button" class="qty-btn qty-btn--plus" aria-label="Aumenta quantità">&plus;</button>
		</div>
		<button type="submit" class="single_add_to_cart_button button alt motta-button">
			<span class="add-to-cart-text">Aggiungi al carrello</span>
		</button>
	</form>
	<a href="{{ route('product', $product->slug) }}" class="motta-button motta-button--text motta-button--full-details">Vedi tutti i dettagli</a>
</div>
