@extends('layouts.app')

@section('title', 'Lista desideri - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta woocommerce no-sidebar elementor-default elementor-kit-8')

@section('content')
<div id="page-header" class="page-header">
	<div class="container clearfix">
		<div class="page-header__content">
			<h1 class="page-header__title">Lista desideri</h1>
		</div>
	</div>
</div>

<div id="site-content" class="site-content">
	<div class="container clearfix" style="padding:40px 0;">

		@auth
			@if ($favorites->isEmpty())
				<p class="woocommerce-info">La tua lista desideri è attualmente vuota.</p>
				<p><a class="button wc-backward" href="{{ route('shop') }}">Torna al negozio</a></p>
			@else
				<ul class="products product-card-layout-4 columns-4 mobile-col-2">
					@foreach ($favorites as $favorite)
						@if ($favorite->product)
							<x-product-card-wc :product="$favorite->product" />
						@endif
					@endforeach
				</ul>
			@endif
		@else
			<ul class="products product-card-layout-4 columns-4 mobile-col-2" id="guest-wishlist-list"></ul>
			<p class="woocommerce-info" id="guest-wishlist-empty" style="display:none;">La tua lista desideri è attualmente vuota.</p>
			<p id="guest-wishlist-empty-link" style="display:none;"><a class="button wc-backward" href="{{ route('shop') }}">Torna al negozio</a></p>
			<script>
				(function () {
					var ids = [];
					try { ids = JSON.parse(window.localStorage.getItem('wishlist_ids') || '[]'); } catch (e) {}
					if (!ids.length) {
						document.getElementById('guest-wishlist-empty').style.display = '';
						document.getElementById('guest-wishlist-empty-link').style.display = '';
						return;
					}
					fetch('{{ route('wishlist.render') }}?ids=' + ids.join(','), { headers: { 'Accept': 'application/json' } })
						.then(function (r) { return r.json(); })
						.then(function (data) {
							if (data.html) {
								document.getElementById('guest-wishlist-list').innerHTML = data.html;
							} else {
								document.getElementById('guest-wishlist-empty').style.display = '';
								document.getElementById('guest-wishlist-empty-link').style.display = '';
							}
						});
				})();
			</script>
		@endauth
	</div>
</div>
@endsection
