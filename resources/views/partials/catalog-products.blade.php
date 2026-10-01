@if($products->count())
<ul class="products product-card-layout-4 columns-4 mobile-col-2 product-list-no-desc-mobile mobile-featured-icons--load mobile-show-atc" data-catalog-products>
@foreach($products as $product)
<x-product-card-wc :product="$product" />
@endforeach
</ul>
@if($products->hasMorePages())
<nav class="woocommerce-navigation woocommerce-navigation__catalog next-posts-navigation" data-catalog-pagination>
	<a href="{{ $products->nextPageUrl() }}" class="nav-links motta-button motta-button--bg-color-black motta-button--large">Carica altri prodotti</a>
</nav>
@endif
@else
<div class="woocommerce-info" data-catalog-empty>Nessun prodotto trovato corrispondente alla tua selezione.</div>
@endif
