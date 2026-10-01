@extends('layouts.app')

@section('title', $category->name.' - Rizzo Christian')
@section('body_class', 'archive tax-product_cat wp-theme-motta wp-child-theme-motta-child theme-motta woocommerce woocommerce-page woocommerce-no-js product-card-layout-4 product-card-mobile-show-atc hfeed sidebar-content motta-shape--round motta-navigation-bar-show motta-catalog-page catalog-view-4 elementor-default elementor-kit-8 motta-blog-page')

@section('content')

<div id="page-header" class="page-header page-header--products page-header--standard page-header--text-custom">
	<div class="container clearfix">
		<nav class="woocommerce-breadcrumb site-breadcrumb"><a href="{{ route('home') }}">Home</a><span class="motta-svg-icon motta-svg-icon--right" ><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M11.42 29.42l-2.84-2.84 10.6-10.58-10.6-10.58 2.84-2.84 13.4 13.42z"></path></svg></span>{{ $category->name }}</nav>
		<div class="page-header__content">
			{{-- Hero image is locked in catalog.css (slider-zermatt-pellets-2.jpg) for all categories. --}}
			<div class="page-header__image" aria-hidden="true"></div>
			<div class="page-header__image-overlay"></div>
			<h1 class="page-header__title">{{ $category->name }}</h1>
		</div>
	</div>
</div>

<div id="site-content" class="site-content">

	<div class="container clearfix site-content-container">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main" data-catalog-main>
				<div class="woocommerce-notices-wrapper"></div>
				<div class="catalog-toolbar">
					<div class="mobile-catalog-toolbar">
						<button type="button" class="mobile-catalog-toolbar__filter-button motta-button--ghost motta-button--color-black hidden-sm hidden-md hidden-lg" data-toggle="off-canvas" data-target="mobile-filter-sidebar-panel">
							<span class="motta-svg-icon motta-svg-icon--filter"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M8 14.4h3.2v-9.6h-3.2v3.2h-4.8v3.2h4.8z"></path><path d="M24 17.6h-3.2v9.6h3.2v-3.2h4.8v-3.2h-4.8z"></path><path d="M14.4 8h14.4v3.2h-14.4v-3.2z"></path><path d="M3.2 20.8h14.4v3.2h-14.4v-3.2z"></path></svg></span>
							Filtra
						</button>
					</div>
					<p class="motta-result-count" data-catalog-count>{{ $products->total() }} risultati</p>
					<div class="catalog-toolbar__toolbar">
						<span class="woocommerce-ordering__label">Ordina per:</span>
						<form class="woocommerce-ordering" method="get" action="{{ route('category', $category->slug) }}" data-catalog-sort>
							<select name="orderby" class="orderby" aria-label="Ordine del negozio">
								<option value="menu_order" {{ request('orderby', 'menu_order') === 'menu_order' ? 'selected' : '' }}>Predefinito</option>
								<option value="date" {{ request('orderby') === 'date' ? 'selected' : '' }}>Più recenti</option>
								<option value="price" {{ request('orderby') === 'price' ? 'selected' : '' }}>Prezzo: dal più basso</option>
								<option value="price-desc" {{ request('orderby') === 'price-desc' ? 'selected' : '' }}>Prezzo: dal più alto</option>
							</select>
						</form>
						<div id="motta-toolbar-view" class="motta-toolbar-view">
							<a href="#" class="grid-2" data-view="grid-2"><span class="motta-svg-icon motta-svg-icon--view-large"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 43 32"><path d="M18.667 6.667v18.667h-12v-18.667h12zM20 5.333h-14.667v21.333h14.667v-21.333z"></path><path d="M36 6.667v18.667h-12v-18.667h12zM37.333 5.333h-14.667v21.333h14.667v-21.333z"></path></svg></span></a>
							<a href="#" class="grid-3" data-view="grid-3"><span class="motta-svg-icon motta-svg-icon--view-medium"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 43 32"><path d="M13.333 6.667v6.667h-6.667v-6.667h6.667zM14.667 5.333h-9.333v9.333h9.333v-9.333z"></path><path d="M25.333 6.667v6.667h-6.667v-6.667h6.667zM26.667 5.333h-9.333v9.333h9.333v-9.333z"></path><path d="M13.333 18.667v6.667h-6.667v-6.667h6.667zM14.667 17.333h-9.333v9.333h9.333v-9.333z"></path><path d="M25.333 18.667v6.667h-6.667v-6.667h6.667zM26.667 17.333h-9.333v9.333h9.333v-9.333z"></path><path d="M37.333 6.667v6.667h-6.667v-6.667h6.667zM38.667 5.333h-9.333v9.333h9.333v-9.333z"></path><path d="M37.333 18.667v6.667h-6.667v-6.667h6.667zM38.667 17.333h-9.333v9.333h9.333v-9.333z"></path></svg></span></a>
							<a href="#" class="grid-4 current" data-view="grid-4"><span class="motta-svg-icon motta-svg-icon--view-small"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 43 32"><path d="M10.667 6.667v2.667h-2.667v-2.667h2.667zM12 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M10.667 14.667v2.667h-2.667v-2.667h2.667zM12 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M10.667 22.667v2.667h-2.667v-2.667h2.667zM12 21.333h-5.333v5.333h5.333v-5.333z"></path><path d="M18.667 6.667v2.667h-2.667v-2.667h2.667zM20 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M18.667 14.667v2.667h-2.667v-2.667h2.667zM20 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M18.667 22.667v2.667h-2.667v-2.667h2.667zM20 21.333h-5.333v5.333h5.333v-5.333z"></path><path d="M26.667 6.667v2.667h-2.667v-2.667h2.667zM28 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M26.667 14.667v2.667h-2.667v-2.667h2.667zM28 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M26.667 22.667v2.667h-2.667v-2.667h2.667zM28 21.333h-5.333v5.333h5.333v-5.333z"></path><path d="M34.667 6.667v2.667h-2.667v-2.667h2.667zM36 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M34.667 14.667v2.667h-2.667v-2.667h2.667zM36 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M34.667 22.667v2.667h-2.667v-2.667h2.667zM36 21.333h-5.333v5.333h5.333v-5.333z"></path></svg></span></a>
							<a href="#" class="grid-5" data-view="grid-5"><span class="motta-svg-icon motta-svg-icon--view-small-extra"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 43 32"><path d="M6.667 6.667v2.667h-2.667v-2.667h2.667zM8 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M6.667 14.667v2.667h-2.667v-2.667h2.667zM8 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M6.667 22.667v2.667h-2.667v-2.667h2.667zM8 21.333h-5.333v5.333h5.333v-5.333z"></path><path d="M14.667 6.667v2.667h-2.667v-2.667h2.667zM16 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M14.667 14.667v2.667h-2.667v-2.667h2.667zM16 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M14.667 22.667v2.667h-2.667v-2.667h2.667zM16 21.333h-5.333v5.333h5.333v-5.333z"></path><path d="M22.667 6.667v2.667h-2.667v-2.667h2.667zM24 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M22.667 14.667v2.667h-2.667v-2.667h2.667zM24 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M22.667 22.667v2.667h-2.667v-2.667h2.667zM24 21.333h-5.333v5.333h5.333v-5.333z"></path><path d="M30.667 6.667v2.667h-2.667v-2.667h2.667zM32 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M30.667 14.667v2.667h-2.667v-2.667h2.667zM32 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M30.667 22.667v2.667h-2.667v-2.667h2.667zM32 21.333h-5.333v5.333h5.333v-5.333z"></path><path d="M38.667 6.667v2.667h-2.667v-2.667h2.667zM40 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M38.667 14.667v2.667h-2.667v-2.667h2.667zM40 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M38.667 22.667v2.667h-2.667v-2.667h2.667zM40 21.333h-5.333v5.333h5.333v-5.333z"></path></svg></span></a>
							<a href="#" class="list" data-view="list"><span class="motta-svg-icon motta-svg-icon--view-list"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 43 32"><path d="M10.667 6.667v2.667h-2.667v-2.667h2.667zM12 5.333h-5.333v5.333h5.333v-5.333z"></path><path d="M10.667 14.667v2.667h-2.667v-2.667h2.667zM12 13.333h-5.333v5.333h5.333v-5.333z"></path><path d="M10.667 22.667v2.667h-2.667v-2.667h2.667zM12 21.333h-5.333v5.333h5.333v-5.333z"></path><path d="M34.667 6.667v2.667h-18.667v-2.667h18.667zM36 5.333h-21.333v5.333h21.333v-5.333z"></path><path d="M34.667 14.667v2.667h-18.667v-2.667h18.667zM36 13.333h-21.333v5.333h21.333v-5.333z"></path><path d="M34.667 22.667v2.667h-18.667v-2.667h18.667zM36 21.333h-21.333v5.333h21.333v-5.333z"></path></svg></span></a>
						</div>
					</div>
				</div>

				<div class="catalog-products-wrap" data-catalog-wrap>
					<div class="catalog-ajax-loader" data-catalog-loader hidden>
						<span class="catalog-ajax-loader__spinner" aria-hidden="true"></span>
					</div>
					<div data-catalog-results>
						@include('partials.catalog-products', ['products' => $products])
					</div>
				</div>
			</main>
		</div>

		<aside id="mobile-filter-sidebar-panel" class="widget-area primary-sidebar catalog-sidebar">
			<div class="sidebar__backdrop"></div>
			<div class="sidebar__container">
				<span class="motta-svg-icon motta-svg-icon--close panel__button-close" role="button" tabindex="0" aria-label="Chiudi"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M28.336 5.936l-2.272-2.272-10.064 10.080-10.064-10.080-2.272 2.272 10.080 10.064-10.080 10.064 2.272 2.272 10.064-10.080 10.064 10.080 2.272-2.272-10.080-10.064z"></path></svg></span>
				<div class="sidebar__header">Filtra e ordina</div>
				<div class="sidebar__content">
					<section class="widget products-filter-widget woocommerce">
						<h4 class="widget-title">
							<span class="motta-svg-icon motta-svg-icon--filter"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 32 32"><path d="M8 14.4h3.2v-9.6h-3.2v3.2h-4.8v3.2h4.8z"></path><path d="M24 17.6h-3.2v9.6h3.2v-3.2h4.8v-3.2h-4.8z"></path><path d="M14.4 8h14.4v3.2h-14.4v-3.2z"></path><path d="M3.2 20.8h14.4v3.2h-14.4v-3.2z"></path></svg></span>
							Filtra
						</h4>

						<form id="catalog-filters" action="{{ route('category', $category->slug) }}" method="get" class="has-collapse ajax-filter" data-catalog-filters>
							@php
								$filterQs = array_filter([
									'price_range' => array_values(array_filter((array) request('price_range', []))),
									'min_price' => request('min_price'),
									'max_price' => request('max_price'),
									'orderby' => request('orderby'),
								], fn ($v) => $v !== null && $v !== '' && $v !== []);
							@endphp
							<div data-catalog-activated>
								@include('partials.catalog-activated-filters', ['category' => $category])
							</div>

							<div class="products-filter__filters filters">
								<div class="products-filter__filter filter product_cat list">
									<span class="products-filter__filter-name filter-name">Categorie</span>
									<div class="products-filter__filter-control filter-control">
										<ul class="products-filter__options products-filter--list filter-list">
											<li class="products-filter__option filter-list-item">
												<a href="{{ route('shop', $filterQs) }}"><span class="products-filter__option-name name">Tutte le categorie</span><span class="products-filter__count counter">{{ $categories->sum('products_count') }}</span></a>
											</li>
											@foreach($categories as $cat)
											<li class="products-filter__option filter-list-item {{ $cat->slug === $category->slug ? 'selected active' : '' }}">
												<a href="{{ route('category', array_merge(['slug' => $cat->slug], $filterQs)) }}"><span class="products-filter__option-name name">{{ $cat->name }}</span><span class="products-filter__count counter">{{ $cat->products_count }}</span></a>
											</li>
											@endforeach
										</ul>
									</div>
								</div>

								<div class="products-filter__filter filter price ranges">
									<span class="products-filter__filter-name filter-name">Prezzo</span>
									<div class="products-filter__filter-control filter-control">
										@php($selectedRanges = (array) request('price_range', []))
										<ul class="products-filter__options products-filter--ranges products-filter--checkboxes filter-ranges">
											@foreach(['0-100' => '0 - €100.00', '100-200' => '€100.00 - €200.00', '250-' => '€250.00+'] as $rangeValue => $rangeLabel)
											@php($isChecked = in_array($rangeValue, $selectedRanges, true))
											<li class="products-filter__option filter-ranges__item filter-checkboxes-item {{ $isChecked ? 'selected' : '' }}" data-filter-checkbox>
												<input type="checkbox" name="price_range[]" value="{{ $rangeValue }}" {{ $isChecked ? 'checked' : '' }}>
												<span class="products-filter__option-name name">{{ $rangeLabel }}</span>
											</li>
											@endforeach
										</ul>
										<div class="product-filter-box">
											<input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="motta-input--base" inputmode="numeric">
											<span class="line"></span>
											<input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="motta-input--base" inputmode="numeric">
											<button type="submit" class="button filter-button motta-button motta-button--bg-color-black motta-button-range">Applica</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</section>
				</div>
			</div>
		</aside>
	</div>
</div>

@endsection
