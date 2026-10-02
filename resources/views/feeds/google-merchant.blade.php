{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
	<title>{{ $feedTitle }}</title>
	<link>{{ url('/') }}</link>
	<description>{{ $feedDescription }}</description>
	<lastBuildDate>{{ $updated }}</lastBuildDate>
	@foreach ($items as $item)
	<item>
		<g:id>{{ $item->id() }}</g:id>
		<g:title>{{ $item->title() }}</g:title>
		<g:description>{{ $item->description() }}</g:description>
		<g:link>{{ $item->link() }}</g:link>
		<g:image_link>{{ $item->imageUrl() }}</g:image_link>
		<g:availability>{{ $item->availability() }}</g:availability>
		<g:condition>new</g:condition>
		@if ($item->brand())
		<g:brand>{{ $item->brand() }}</g:brand>
		@endif
		@if ($item->regularPrice())
		<g:price>{{ $item->regularPrice() }} {{ $item->currency() }}</g:price>
		<g:sale_price>{{ $item->price() }} {{ $item->currency() }}</g:sale_price>
		@if ($item->salePriceEffectiveDate())
		<g:sale_price_effective_date>{{ $item->salePriceEffectiveDate() }}</g:sale_price_effective_date>
		@endif
		@else
		<g:price>{{ $item->price() }} {{ $item->currency() }}</g:price>
		@endif
		@if ($item->gtin())
		<g:gtin>{{ $item->gtin() }}</g:gtin>
		@endif
		@if ($item->mpn())
		<g:mpn>{{ $item->mpn() }}</g:mpn>
		@endif
		@if ($item->hasIdentifierExistsNo())
		<g:identifier_exists>no</g:identifier_exists>
		@endif
		<g:google_product_category>{{ $item->googleProductCategory() }}</g:google_product_category>
		@if ($item->energyEfficiencyClass())
		<g:energy_efficiency_class>{{ $item->energyEfficiencyClass() }}</g:energy_efficiency_class>
		@endif
		<g:shipping>
			<g:country>{{ $shipping['country'] }}</g:country>
			<g:service>{{ $shipping['service'] }}</g:service>
			<g:price>{{ $shipping['price'] }} {{ $shipping['currency'] }}</g:price>
			<g:min_handling_time>{{ $shipping['handling_min'] }}</g:min_handling_time>
			<g:max_handling_time>{{ $shipping['handling_max'] }}</g:max_handling_time>
			<g:min_transit_time>{{ $shipping['transit_min'] }}</g:min_transit_time>
			<g:max_transit_time>{{ $shipping['transit_max'] }}</g:max_transit_time>
		</g:shipping>
	</item>
	@endforeach
</channel>
</rss>
