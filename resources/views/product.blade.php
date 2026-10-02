@extends('layouts.app')

@section('title', $product->name.' - Rizzo Christian')
@section('body_class', 'single single-product postid-'.$product->id.' woocommerce woocommerce-page theme-motta no-sidebar elementor-default elementor-kit-8')

@section('content')
@include('partials.product-schema')

<div id="site-content" class="site-content">

	<div class="container clearfix ">
	<div id="primary" class="content-area"><main id="main" class="site-main" role="main"><div class="motta-breadcrumb-social-wrapper"><nav class="woocommerce-breadcrumb site-breadcrumb"><a href="{{ route('home') }}">Home</a><span class="motta-svg-icon motta-svg-icon--right" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M11.42 29.42l-2.84-2.84 10.6-10.58-10.6-10.58 2.84-2.84 13.4 13.42z"></path></svg></span>@if($product->category)<a href="{{ route('category', $product->category->slug) }}">{{ $product->category->name }}</a>@endif<span class="motta-svg-icon motta-svg-icon--right" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M11.42 29.42l-2.84-2.84 10.6-10.58-10.6-10.58 2.84-2.84 13.4 13.42z"></path></svg></span>{{ $product->name }}</nav><div class="motta-product-quick-links">
<a href="#" class="motta-button motta-button--text motta-button--product-share" data-toggle="modal" data-target="socials-popup" role="button">
	<span class="motta-button__icon"><span class="motta-svg-icon motta-svg-icon--share-mini" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.50002 5.89999V7.09999H8.90002V12.9H2.10002V7.09999H3.50002V5.89999H0.900024V14.1H10.1V5.89999H7.50002Z" fill="currentColor"/><path d="M4.90002 2.94999V9.99999H6.10002V2.94999L8.08002 4.91999L8.92002 4.07999L5.50002 0.649994L2.08002 4.07999L2.92002 4.91999L4.90002 2.94999Z" fill="currentColor"/></svg></span></span>
	<span class="motta-button__text ">Condividi</span>
</a>

 <a href="#" class="motta-button  motta-button--text motta-button--product-print">
	<span class="motta-button__icon"><span class="motta-svg-icon motta-svg-icon--print" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M22.857 11.429v-9.143h-13.714v9.143h-6.857v13.714h6.857v4.571h13.714v-4.571h6.857v-13.714h-6.857zM11.886 5.029h8.229v6.4h-8.229v-6.4zM20.114 22.4v4.571h-8.229v-5.943h8.229v1.371zM26.971 22.4h-4.114v-4.114h-13.714v4.114h-4.114v-8.229h21.943v8.229z"></path></svg></span></span>
	<span class="motta-button__text ">Stampa</span>
</a></div></div>
					
			<div class="woocommerce-notices-wrapper"></div><div id="product-{{ $product->id }}" class="has-buy-now layout-1 product type-product post-{{ $product->id }} status-publish {{ $product->in_stock ? 'instock' : 'outofstock' }} {{ $product->category ? 'product_cat-'.$product->category->slug : '' }} has-post-thumbnail {{ $product->isOnSale() ? 'sale' : '' }} shipping-taxable purchasable product-type-simple">

	<div class="product-header-compact"><div class="product-header-main">
	<a href="{{ route('home') }}" class="motta-button  motta-button--text motta-button--history">
		<span class="motta-button__icon"><span class="motta-svg-icon motta-svg-icon--left" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M20.58 2.58l2.84 2.84-10.6 10.58 10.6 10.58-2.84 2.84-13.4-13.42z"></path></svg></span></span>
	</a>
	
<a href="#" class="motta-button motta-button--text motta-button--product-share" data-toggle="modal" data-target="socials-popup" role="button">
	<span class="motta-button__icon"><span class="motta-svg-icon motta-svg-icon--share" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"> <path d="M20.8 12.8v3.2h3.2v9.6h-16v-9.6h3.2v-3.2h-6.4v16h22.4v-16z"></path> <path d="M14.4 7.84v12.96h3.2v-12.96l3.68 3.68 2.24-2.24-7.52-7.52-7.52 7.52 2.24 2.24z"></path> </svg></span></span>
	<span class="motta-button__text ">Condividi</span>
</a>
</div><div class="product-sticky-header">
	<a href="{{ route('home') }}" class="motta-button motta-button--text motta-button--history">
		<span class="motta-svg-icon motta-svg-icon--left" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M20.58 2.58l2.84 2.84-10.6 10.58 10.6 10.58-2.84 2.84-13.4-13.42z"></path></svg></span>	</a>
	<div class="product-info">
		<span class="product-title">{{ $product->name }}</span>
		<span class="product-price">
		@if($product->isOnSale())<ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->price,2) }}</span></ins> <del aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->regular_price,2) }}</span></del>@else<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->price,2) }}</span>@endif		</span>
	</div>
	<div class="product-buttons">
		
<a href="#" class="motta-button motta-button--text motta-button--product-share" data-toggle="modal" data-target="socials-popup" role="button">
	<span class="motta-button__icon"><span class="motta-svg-icon motta-svg-icon--share" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"> <path d="M20.8 12.8v3.2h3.2v9.6h-16v-9.6h3.2v-3.2h-6.4v16h22.4v-16z"></path> <path d="M14.4 7.84v12.96h3.2v-12.96l3.68 3.68 2.24-2.24-7.52-7.52-7.52 7.52 2.24 2.24z"></path> </svg></span></span>
	<span class="motta-button__text ">Condividi</span>
</a>
		<a href="#" class="motta-button motta-button--text motta-button--product-more" data-toggle="modal" data-target="product-more-popup">
			<span class="motta-svg-icon motta-svg-icon--more" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"> <path d="M8 16c0 1.767-1.433 3.2-3.2 3.2s-3.2-1.433-3.2-3.2c0-1.767 1.433-3.2 3.2-3.2s3.2 1.433 3.2 3.2z"></path> <path d="M19.2 16c0 1.767-1.433 3.2-3.2 3.2s-3.2-1.433-3.2-3.2c0-1.767 1.433-3.2 3.2-3.2s3.2 1.433 3.2 3.2z"></path> <path d="M30.4 16c0 1.767-1.433 3.2-3.2 3.2s-3.2-1.433-3.2-3.2c0-1.767 1.433-3.2 3.2-3.2s3.2 1.433 3.2 3.2z"></path> </svg></span>		</a>
	</div>
</div></div><div class="product-gallery-summary product-image-zoom"><div class="motta-product-gallery"><div class="woocommerce-product-gallery woocommerce-product-gallery--with-images woocommerce-product-gallery--columns-5 images" data-columns="5">
	@php
		$galleryImages = $product->images;
		if ($galleryImages->isEmpty() && $product->image) {
			$galleryImages = collect([(object) ['path' => $product->image]]);
		}
		$mainSrc = $product->imageUrl();
	@endphp
	<div class="woocommerce-product-gallery__wrapper">
	<div class="product-gallery__main-image" style="margin-bottom:12px;">
		<img id="product-gallery-main-img" src="{{ $mainSrc }}" width="600" height="600" alt="{{ $product->name }}" style="width:100%;height:auto;border-radius:8px;" fetchpriority="high" decoding="async">
	</div>
	@if($galleryImages->count() > 1)
	<div class="product-gallery__thumbs" style="display:flex;gap:10px;flex-wrap:wrap;">
		@foreach($galleryImages as $i => $image)
		@php
			$thumbPath = ltrim(str_replace('\\', '/', $image->path ?? ''), '/');
			$thumbUrl = (is_file(public_path($thumbPath)) && filesize(public_path($thumbPath)) > 0)
				? asset($thumbPath)
				: $mainSrc;
		@endphp
		<button type="button" class="product-gallery__thumb-btn" data-full="{{ $thumbUrl }}" style="border:2px solid {{ $i === 0 ? '#1f1a17' : 'transparent' }};padding:0;border-radius:6px;overflow:hidden;cursor:pointer;background:none;">
			<img src="{{ $thumbUrl }}" width="64" height="64" alt="{{ $product->name }}" loading="lazy" style="display:block;width:64px;height:64px;object-fit:cover;">
		</button>
		@endforeach
	</div>
	@endif
	</div></div>
<div class="motta-product-images-buttons">
<a href="#" class="motta-button motta-button--icon motta-button--raised motta-shape--circle motta-button--product-lightbox">
	<span class="motta-svg-icon motta-svg-icon--full-screen" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M14.667 24h-6.667v-6.667h2.667v4h4z"></path><path d="M10.667 14.667h-2.667v-6.667h6.667v2.667h-4z"></path><path d="M24 14.667h-2.667v-4h-4v-2.667h6.667z"></path><path d="M24 24h-6.667v-2.667h4v-4h2.667z"></path></svg></span></a></div><div class="product-featured-icons"><a href="#" data-product_id="{{ $product->id }}" data-product_title="{{ $product->name }}" class="wcboost-products-compare-button wcboost-products-compare-button--ajax motta-button motta-button--text motta-button-compare--remove" aria-label="Confronta &ldquo;{{ $product->name }}&rdquo;" role="button">
				<span class="wcboost-products-compare-button__icon"><span class="motta-svg-icon motta-svg-icon--compare" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M13.136 14.864l-3.68-3.664h16.144v-3.2h-16.144l3.68-3.664-2.272-2.272-7.52 7.536 7.52 7.536z"></path><path d="M21.136 14.864l-2.272 2.272 3.68 3.664h-16.144v3.2h16.144l-3.68 3.664 2.272 2.272 7.52-7.536z"></path></svg></span></span>
				<span class="wcboost-products-compare-button__text" data-add="Aggiungi al confronto" data-remove="Rimuovi dal confronto" data-view="Sfoglia confronti">Confronta</span>
			</a><a href="{{ route('wishlist.toggle', $product) }}" data-url="{{ route('wishlist.toggle', $product) }}" data-quantity="1" data-product_id="{{ $product->id }}"  data-product_title="{{ $product->name }}" data-variations="" class="wcboost-wishlist-button wishlist-toggle wcboost-wishlist-button--theme button wp-element-button wcboost-wishlist-button--ajax motta-button motta-button--text motta-button--wishlist motta-button-wishlist--view {{ auth()->check() && auth()->user()->favorites()->where('product_id', $product->id)->exists() ? 'is-active' : '' }}" aria-label="Aggiungi &ldquo;{{ $product->name }}&rdquo; alla lista dei desideri">
				<span class="motta-button__icon add-to-wishlist-button__icon wcboost-wishlist-button__icon"><span class="motta-svg-icon motta-svg-icon--wishlist" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M22.736 6.4v0c1.792 0 3.44 1.12 4.128 2.768 0.8 1.92 0.112 4.144-1.856 6.112l-9.024 8.992-9.024-8.976c-1.984-1.984-2.64-4.144-1.824-6.080 0.688-1.68 2.352-2.8 4.144-2.8 1.504 0 3.040 0.752 4.448 2.16l2.256 2.256 2.256-2.256c1.44-1.424 2.992-2.176 4.496-2.176zM22.736 3.2c-2.176 0-4.544 0.912-6.752 3.104-2.192-2.176-4.544-3.088-6.704-3.088-6.368 0-11.040 7.904-4.576 14.336l11.28 11.248 11.28-11.248c6.496-6.448 1.856-14.352-4.528-14.352v0z"></path></svg></span></span>
				<span class="motta-button__text add-to-wishlist-button__text wcboost-wishlist-button__text" data-add="Aggiungi alla lista desideri" data-remove="Rimuovi dalla lista" data-view="Vedi lista desideri">Lista desideri</span>
			</a></div><div class="product-fixed-gallery-spacing"></div></div>
	<div class="summary entry-summary">
		<h1 class="product_title entry-title">{{ $product->name }}</h1><div class="product-meta-wrapper"><div class="meta meta-cat">in @if($product->category)<a href="{{ route('category', $product->category->slug) }}">{{ $product->category->name }}</a>@endif</div></div><div class="motta-price-stock variations-attribute-change"><p class="price">
@if($product->isOnSale())
<ins><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->price, 2) }}</bdi></span></ins> <del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->regular_price, 2) }}</bdi></span></del><span class="price__save"><span class="text">Risparmi:</span><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->regular_price - $product->price, 2) }}</bdi></span></span>
@else
<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&euro;</span>{{ number_format($product->price, 2) }}</bdi></span>
@endif
</p>
<p class="stock {{ $product->in_stock ? 'in-stock' : 'out-of-stock' }}">{{ $product->in_stock ? 'Disponibile' : 'Esaurito' }}</p>
</div>
@php($energyClass = (new \App\Support\MerchantListing($product))->energyEfficiencyClass())
@php($merchantBrand = (new \App\Support\MerchantListing($product))->brand())
@if ($merchantBrand)
<p class="ph-product-brand">Marca: {{ $merchantBrand }}</p>
@endif
@if ($energyClass)
<p class="ph-energy-class">Classe di efficienza energetica: {{ $energyClass }}</p>
@endif
	
	@if ($product->in_stock)
	<form class="cart ph-product-cart" action="{{ route('cart.add', $product) }}" method="post">
@csrf
<div class="quantity motta-qty-stepper">
	<button type="button" class="qty-btn qty-btn--minus" aria-label="Diminuisci quantità">&minus;</button>
	<input type="number" name="quantity" value="1" min="1" class="input-text qty text" inputmode="numeric">
	<button type="button" class="qty-btn qty-btn--plus" aria-label="Aumenta quantità">&plus;</button>
</div>
<button type="submit" name="add-to-cart" value="{{ $product->id }}" class="single_add_to_cart_button button alt motta-button">
			<span class="motta-svg-icon motta-svg-icon--cart-trolley single_add_to_cart_button--icon" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M25.248 22.4l3.552-14.4h-18.528l-0.96-4.8h-6.112v3.2h3.488l3.2 16h15.36zM24.704 11.2l-1.968 8h-10.24l-1.6-8h13.808z"></path><path d="M25.6 26.4c0 1.325-1.075 2.4-2.4 2.4s-2.4-1.075-2.4-2.4c0-1.325 1.075-2.4 2.4-2.4s2.4 1.075 2.4 2.4z"></path><path d="M14.4 26.4c0 1.325-1.075 2.4-2.4 2.4s-2.4-1.075-2.4-2.4c0-1.325 1.075-2.4 2.4-2.4s2.4 1.075 2.4 2.4z"></path></svg></span>			<span class="single_add_to_cart_button--text">Aggiungi al carrello</span>		</button>
<button type="submit" formaction="{{ route('cart.add', $product) }}" name="buy-now" value="1" class="motta-buy-now-button motta-button motta-button--ghost">Acquista ora</button>
</form>
	@else
	<p class="stock out-of-stock">Questo articolo non è attualmente disponibile all’acquisto.</p>
	@endif
	@include('partials.purchase-terms')

	
<div class="product-featured-icons"><a href="#" data-product_id="{{ $product->id }}" data-product_title="{{ $product->name }}" class="wcboost-products-compare-button wcboost-products-compare-button--ajax motta-button motta-button--text motta-button-compare--remove" aria-label="Confronta &ldquo;{{ $product->name }}&rdquo;" role="button">
				<span class="wcboost-products-compare-button__icon"><span class="motta-svg-icon motta-svg-icon--compare" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M13.136 14.864l-3.68-3.664h16.144v-3.2h-16.144l3.68-3.664-2.272-2.272-7.52 7.536 7.52 7.536z"></path><path d="M21.136 14.864l-2.272 2.272 3.68 3.664h-16.144v3.2h16.144l-3.68 3.664 2.272 2.272 7.52-7.536z"></path></svg></span></span>
				<span class="wcboost-products-compare-button__text" data-add="Aggiungi al confronto" data-remove="Rimuovi dal confronto" data-view="Sfoglia confronti">Confronta</span>
			</a><a href="{{ route('wishlist.toggle', $product) }}" data-url="{{ route('wishlist.toggle', $product) }}" data-quantity="1" data-product_id="{{ $product->id }}"  data-product_title="{{ $product->name }}" data-variations="" class="wcboost-wishlist-button wishlist-toggle wcboost-wishlist-button--theme button wp-element-button wcboost-wishlist-button--ajax motta-button motta-button--text motta-button--wishlist motta-button-wishlist--view {{ auth()->check() && auth()->user()->favorites()->where('product_id', $product->id)->exists() ? 'is-active' : '' }}" aria-label="Aggiungi &ldquo;{{ $product->name }}&rdquo; alla lista dei desideri">
				<span class="motta-button__icon add-to-wishlist-button__icon wcboost-wishlist-button__icon"><span class="motta-svg-icon motta-svg-icon--wishlist" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M22.736 6.4v0c1.792 0 3.44 1.12 4.128 2.768 0.8 1.92 0.112 4.144-1.856 6.112l-9.024 8.992-9.024-8.976c-1.984-1.984-2.64-4.144-1.824-6.080 0.688-1.68 2.352-2.8 4.144-2.8 1.504 0 3.040 0.752 4.448 2.16l2.256 2.256 2.256-2.256c1.44-1.424 2.992-2.176 4.496-2.176zM22.736 3.2c-2.176 0-4.544 0.912-6.752 3.104-2.192-2.176-4.544-3.088-6.704-3.088-6.368 0-11.040 7.904-4.576 14.336l11.28 11.248 11.28-11.248c6.496-6.448 1.856-14.352-4.528-14.352v0z"></path></svg></span></span>
				<span class="motta-button__text add-to-wishlist-button__text wcboost-wishlist-button__text" data-add="Aggiungi alla lista desideri" data-remove="Rimuovi dalla lista" data-view="Vedi lista desideri">Lista desideri</span>
			</a></div><div class="product_meta">

	
	
	
	
	
</div>
<div class="single-product-extra-content"><section id="motta-icon-box-1" class="widget icon-box-widget"><div class="motta-icon-box-widget">					<div class="motta-icon-box-widget__item">
													<span class="motta-icon-box-widget__icon motta-svg-icon" ><svg viewbox="0 0 32 32">
<path d="M32 19.429c0-2.331-1.417-4.343-3.429-5.234v-2.011l-4-5.326h-4v-2.286h-18.286v2.286h16v9.143h8c1.897 0 3.429 1.531 3.429 3.429v3.429h-3.589c-0.503-1.966-2.286-3.429-4.411-3.429s-3.909 1.463-4.411 3.429h-7.177c-0.503-1.966-2.286-3.429-4.411-3.429-2.514 0-4.571 2.057-4.571 4.571s2.057 4.571 4.571 4.571c2.126 0 3.909-1.463 4.411-3.429h7.177c0.503 1.966 2.286 3.429 4.411 3.429s3.909-1.463 4.411-3.429h5.874v-5.714zM23.040 13.714h-2.469v-4.571h2.857l2.857 3.817v0.754h-3.246zM5.714 26.286c-1.257 0-2.286-1.029-2.286-2.286s1.029-2.286 2.286-2.286 2.286 1.029 2.286 2.286-1.029 2.286-2.286 2.286zM21.714 26.286c-1.257 0-2.286-1.029-2.286-2.286s1.029-2.286 2.286-2.286 2.286 1.029 2.286 2.286-1.029 2.286-2.286 2.286z"></path>
<path d="M0 9.143h9.143v2.286h-9.143v-2.286z"></path>
<path d="M4.571 13.714h9.143v2.263h-9.143v-2.263z"></path>
</svg></span>
						
																					<a href="{{ url('/spedizione-e-consegna/') }}" class="motta-icon-box-widget__text motta-button motta-button--text">Spedizione gratuita in Italia</a>
													
																					<a href="{{ url('/spedizione-e-consegna/') }}" class="motta-icon-box-widget__button motta-button motta-button--text">Dettagli</a>
																		</div>
										<div class="motta-icon-box-widget__item">
													<span class="motta-icon-box-widget__icon motta-svg-icon" ><svg viewbox="0 0 32 32">
<path d="M25.531 2.286h-16.777l-4.183 8.8v18.629h25.143v-18.629l-4.183-8.8zM26.72 11.2h-8.206v-6.171h5.28l2.926 6.171zM10.491 5.029h5.28v6.171h-8.206l2.926-6.171zM7.314 26.971v-13.029h8.457v4.343h2.743v-4.343h8.457v13.029h-19.657z"></path>
</svg></span>
						
																					<a href="{{ url('/resi-e-rimborsi/') }}" class="motta-icon-box-widget__text motta-button motta-button--text">Reso entro 14 giorni</a>
													
																					<a href="{{ url('/resi-e-rimborsi/') }}" class="motta-icon-box-widget__button motta-button motta-button--text">Dettagli</a>
																		</div>
					</div></section></div>	</div>

	</div>
	<div class="woocommerce-tabs wc-tabs-wrapper ">
		<ul class="motta-tabs-heading tabs wc-tabs" role="tablist">
							<li role="presentation" class="description_tab" id="tab-title-description" role="tab" aria-controls="tab-description">
					<a href="#tab-description" role="tab" aria-controls="tab-description">
						Descrizione					</a>
				</li>
									</ul>
					<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--description panel entry-content wc-tab" id="tab-description" role="tabpanel" aria-labelledby="tab-title-description">@include('partials.product-description')</div>
		
			</div>


	<section class="related products">

					<h2>Prodotti simili</h2>
				<ul class="products product-card-layout-4 columns-5 mobile-col-2 mobile-featured-icons--load mobile-show-atc">
@foreach($related as $relatedProduct)
<x-product-card-wc :product="$relatedProduct" />
@endforeach
</ul>

	</section>
	</div>


		
	</main></div>
	

</div></div><!-- #content -->
	
@endsection
