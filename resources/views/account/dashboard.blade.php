@extends('layouts.app')

@section('title', 'Il mio account - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta no-sidebar elementor-default elementor-kit-8')

@section('content')
<div id="page-header" class="page-header">
	<div class="container clearfix">
		<div class="page-header__content">
			<h1 class="page-header__title">Il mio account</h1>
		</div>
	</div>
</div>

<div id="site-content" class="site-content">
	<div class="container clearfix" style="padding:40px 0;">

		@if (session('success'))
			<p class="woocommerce-message">{{ session('success') }}</p>
		@endif

		<p>Bentornato, <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }}).</p>

		<form method="POST" action="{{ route('logout') }}" style="margin:10px 0 30px;">
			@csrf
			<button type="submit" class="button motta-button--ghost">Esci</button>
		</form>

		<h2>I miei ordini</h2>
		@if ($orders->isEmpty())
			<p class="woocommerce-info">Non hai ancora effettuato ordini.</p>
			<p><a class="button wc-backward" href="{{ route('shop') }}">Acquista ora</a></p>
		@else
			<table class="shop_table shop_table_responsive" cellspacing="0" style="width:100%;">
				<thead>
					<tr>
						<th>Ordine</th>
						<th>Data</th>
						<th>Stato</th>
						<th>Totale</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($orders as $order)
						<tr>
							<td>#{{ $order->reference }}</td>
							<td>{{ $order->created_at->format('d.m.Y') }}</td>
							<td>{{ $order->statusLabel() }}</td>
							<td>{{ number_format($order->total, 2) }} &euro;</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		@endif
	</div>
</div>
@endsection
