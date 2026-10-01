{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
	<title>Rizzo Christian</title>
	<link>{{ url('/') }}</link>
	<description>Legna da ardere, pellet, bricchetti e stufe a pellet. Prezzi IVA inclusa. Spedizione gratuita in tutta Italia: preparazione 1-2 giorni lavorativi, spedizione 1-2 giorni lavorativi, consegna totale 2-4 giorni lavorativi.</description>
	<lastBuildDate>{{ $updated }}</lastBuildDate>
	@foreach ($items as $item)
	<item>
		<g:id>{{ $item->id() }}</g:id>
		<g:title>{{ $item->title() }}</g:title>
		<g:description>{{ $item->description() }}</g:description>
		<g:link>{{ route('product', $item->product->slug) }}</g:link>
		<g:image_link>{{ $item->imageUrl() }}</g:image_link>
		<g:availability>{{ $item->availability() }}</g:availability>
		<g:condition>new</g:condition>
		<g:brand>{{ $item->brand() }}</g:brand>
		@if ($item->regularPrice())
		<g:price>{{ $item->regularPrice() }} EUR</g:price>
		<g:sale_price>{{ $item->price() }} EUR</g:sale_price>
		@else
		<g:price>{{ $item->price() }} EUR</g:price>
		@endif
		@if ($item->gtin())
		<g:gtin>{{ $item->gtin() }}</g:gtin>
		<g:identifier_exists>yes</g:identifier_exists>
		@else
		<g:identifier_exists>no</g:identifier_exists>
		@endif
		<g:google_product_category>{{ $item->googleProductCategory() }}</g:google_product_category>
		@if ($item->energyEfficiencyClass())
		<g:energy_efficiency_class>{{ $item->energyEfficiencyClass() }}</g:energy_efficiency_class>
		@endif
		<g:shipping>
			<g:country>IT</g:country>
			<g:service>Spedizione gratuita in Italia</g:service>
			<g:price>0.00 EUR</g:price>
			<g:min_handling_time>1</g:min_handling_time>
			<g:max_handling_time>2</g:max_handling_time>
			<g:min_transit_time>1</g:min_transit_time>
			<g:max_transit_time>2</g:max_transit_time>
		</g:shipping>
	</item>
	@endforeach
</channel>
</rss>
