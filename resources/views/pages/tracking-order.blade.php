@extends('layouts.app')

@section('title', 'Traccia ordine - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta no-sidebar elementor-default elementor-kit-8')

@section('content')
<div id="site-content" class="site-content pages-shell">
	<div class="container clearfix">
		<section class="track-page">
			<h1 class="track-page__title">Traccia ordine</h1>
			<p class="track-page__intro">
				Per tracciare il tuo ordine, inserisci l’ID ordine nella casella sottostante e premi il pulsante «Traccia». Lo trovi sulla ricevuta e nell’e-mail di conferma che dovresti aver ricevuto.
			</p>

			<div class="track-card">
				<form method="GET" action="{{ route('tracking-order') }}" class="track-form">
					<div class="track-form__row">
						<div class="track-field">
							<label for="order_id">ID ordine</label>
							<input id="order_id" type="text" name="order_id" value="{{ $orderId }}" placeholder="Lo trovi nell’e-mail di conferma dell’ordine" required>
						</div>
						<div class="track-field">
							<label for="track_email">E-mail di fatturazione</label>
							<input id="track_email" type="email" name="email" value="{{ $email }}" placeholder="L’indirizzo e-mail utilizzato al momento dell’ordine" required>
						</div>
					</div>
					<button type="submit" class="track-submit">Traccia</button>
				</form>

				@if ($notFound)
					<div class="pages-alert pages-alert--error">
						Nessun ordine trovato con questi dati.
					</div>
				@endif

				@if ($order)
					<div class="track-result">
						<div class="track-result__header">
							<strong>Ordine {{ $order->reference }}</strong>
							<span class="track-result__status">{{ $order->statusLabel() }}</span>
						</div>
						<p>E-mail: {{ $order->email }}</p>
						<p>Data: {{ $order->created_at?->format('d.m.Y H:i') }}</p>
						<p>Totale: €{{ number_format((float) $order->total, 2) }}</p>
						@if ($order->items->isNotEmpty())
							<ul class="track-result__items">
								@foreach ($order->items as $item)
									<li>
										<span>{{ $item->product_name ?: ($item->product?->name ?? 'Prodotto') }}</span>
										<span>× {{ $item->quantity }}</span>
										<span>€{{ number_format((float) $item->unit_price, 2) }}</span>
									</li>
								@endforeach
							</ul>
						@endif
					</div>
				@endif
			</div>
		</section>
	</div>
</div>
@endsection
