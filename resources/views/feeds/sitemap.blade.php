{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($staticUrls as $url)
	<url>
		<loc>{{ $url }}</loc>
		<changefreq>weekly</changefreq>
		<priority>0.6</priority>
	</url>
@endforeach
@foreach ($categories as $category)
	<url>
		<loc>{{ route('category', $category->slug) }}</loc>
		@if ($category->updated_at)
		<lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
		@endif
		<changefreq>weekly</changefreq>
		<priority>0.7</priority>
	</url>
@endforeach
@foreach ($products as $product)
	<url>
		<loc>{{ route('product', $product->slug) }}</loc>
		@if ($product->updated_at)
		<lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
		@endif
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>
@endforeach
</urlset>
