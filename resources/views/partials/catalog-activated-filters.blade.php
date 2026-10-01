@php
	$selectedRanges = array_values(array_filter((array) request('price_range', [])));
	$minPrice = request('min_price');
	$maxPrice = request('max_price');
	$hasPriceFilter = $selectedRanges || ($minPrice !== null && $minPrice !== '') || ($maxPrice !== null && $maxPrice !== '');
	$selectedCats = array_values(array_filter(
		array_map('strval', (array) ($selectedCategories ?? request('product_cat', []))),
		fn (string $slug) => $slug !== '' && $slug !== '0'
	));
	$rangeLabels = [
		'0-100' => '0 - €100.00',
		'100-200' => '€100.00 - €200.00',
		'250-' => '€250.00+',
	];
	$priceQs = array_filter([
		'price_range' => $selectedRanges ?: null,
		'min_price' => ($minPrice !== null && $minPrice !== '') ? $minPrice : null,
		'max_price' => ($maxPrice !== null && $maxPrice !== '') ? $maxPrice : null,
		'orderby' => request('orderby') ?: null,
	], fn ($v) => $v !== null && $v !== '' && $v !== []);
	$hasAnyFilter = $hasPriceFilter || isset($category) || count($selectedCats) > 0;
@endphp

<div class="products-filter__activated" data-filter-activated>
	<div class="products-filter__activated-heading">
		<h6>Affina per</h6>
		@if($hasAnyFilter)
			<a href="{{ route('shop') }}" class="reset-button" data-filter-clear>Cancella tutto</a>
		@endif
	</div>
	<div class="products-filter__activated-items">
		@if(isset($category))
			<a href="{{ route('shop', $priceQs) }}" class="remove-filtered" data-filter-remove="category">{{ $category->name }} <span aria-hidden="true">×</span></a>
		@endif
		@foreach($selectedCats as $catSlug)
			@php($catLabel = collect($categories ?? [])->firstWhere('slug', $catSlug)?->name ?? $catSlug)
			<a href="#" class="remove-filtered" data-filter-remove="product_cat" data-value="{{ $catSlug }}">{{ $catLabel }} <span aria-hidden="true">×</span></a>
		@endforeach
		@foreach($selectedRanges as $range)
			<a href="#" class="remove-filtered" data-filter-remove="price_range" data-value="{{ $range }}">Prezzo: {{ $rangeLabels[$range] ?? $range }} <span aria-hidden="true">×</span></a>
		@endforeach
		@if(($minPrice !== null && $minPrice !== '') || ($maxPrice !== null && $maxPrice !== ''))
			<a href="#" class="remove-filtered" data-filter-remove="price_custom">Prezzo: €{{ $minPrice !== null && $minPrice !== '' ? number_format((float) $minPrice, 2) : '0.00' }} - €{{ $maxPrice !== null && $maxPrice !== '' ? number_format((float) $maxPrice, 2) : '…' }} <span aria-hidden="true">×</span></a>
		@endif
	</div>
</div>
