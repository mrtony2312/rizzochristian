@foreach($products as $product)
<x-product-card-wc :product="$product" />
@endforeach
