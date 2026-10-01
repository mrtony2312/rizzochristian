@extends('layouts.app')

@section('title', 'Cassa - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta woocommerce woocommerce-checkout no-sidebar elementor-default elementor-kit-8')

@section('content')
@php
	$provinces = config('billing.provinces');
@endphp

<div id="page-header" class="page-header page-header--checkout">
	<div class="container clearfix">
		<div class="page-header__content">
			<h1 class="page-header__title">Cassa</h1>
		</div>
	</div>
</div>

<div id="site-content" class="site-content">
	<div class="container clearfix ph-checkout">
		@if ($errors->any())
			<div class="woocommerce-error ph-checkout__errors">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form method="POST" action="{{ route('checkout.store') }}" class="ph-checkout__form" id="checkout-form">
			@csrf
			<div class="ph-checkout__layout">
				<div class="ph-checkout__main">
					<section class="ph-checkout__section">
						<h2 class="ph-checkout__section-title">Informazioni di contatto</h2>
						<label class="ph-field">
							<span class="ph-field__label">Indirizzo e-mail</span>
							<input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required autocomplete="email">
						</label>
						<p class="ph-checkout__guest-note">Stai effettuando l’ordine come ospite.</p>
					</section>

					<section class="ph-checkout__section">
						<h2 class="ph-checkout__section-title">Indirizzo di fatturazione</h2>

						<label class="ph-field">
							<span class="ph-field__label">Paese</span>
							<select name="country" required>
								@foreach (config('billing.countries') as $code => $label)
									<option value="{{ $code }}" {{ old('country', 'IT') === $code ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</label>

						<div class="ph-field-row">
							<label class="ph-field">
								<span class="ph-field__label">Nome</span>
								<input type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name">
							</label>
							<label class="ph-field">
								<span class="ph-field__label">Cognome</span>
								<input type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name">
							</label>
						</div>

						<label class="ph-field">
							<span class="ph-field__label">Azienda (opzionale)</span>
							<input type="text" name="company" value="{{ old('company') }}" autocomplete="organization">
						</label>

						<label class="ph-field">
							<span class="ph-field__label">Indirizzo</span>
							<input type="text" name="address" value="{{ old('address') }}" required autocomplete="address-line1">
						</label>

						<div class="ph-checkout__address2" data-address2-wrap @if(old('address_2')) style="display:block" @endif>
							<label class="ph-field">
								<span class="ph-field__label">Appartamento, interno, ecc. (opzionale)</span>
								<input type="text" name="address_2" value="{{ old('address_2') }}" autocomplete="address-line2">
							</label>
						</div>
						@unless(old('address_2'))
							<button type="button" class="ph-checkout__add-line" data-toggle-address2>+ Aggiungi appartamento, interno, ecc.</button>
						@endunless

						<div class="ph-field-row">
							<label class="ph-field">
								<span class="ph-field__label">CAP</span>
								<input type="text" name="postal_code" value="{{ old('postal_code') }}" required autocomplete="postal-code">
							</label>
							<label class="ph-field">
								<span class="ph-field__label">Città</span>
								<input type="text" name="city" value="{{ old('city') }}" required autocomplete="address-level2">
							</label>
						</div>

						<div class="ph-field-row">
							<label class="ph-field">
								<span class="ph-field__label">Provincia (opzionale)</span>
								<select name="state">
									<option value="">—</option>
									@foreach($provinces as $code => $label)
										<option value="{{ $code }}" {{ old('state') === $code ? 'selected' : '' }}>{{ $label }}</option>
									@endforeach
								</select>
							</label>
							<label class="ph-field">
								<span class="ph-field__label">Telefono</span>
								<input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel">
							</label>
						</div>
					</section>

					<section class="ph-checkout__section">
						<h2 class="ph-checkout__section-title">Opzioni di pagamento</h2>
						<label class="ph-payment-option is-selected">
							<input type="radio" name="payment_method" value="bonifico" checked>
							<span class="ph-payment-option__body">
								<span class="ph-payment-option__title">Bonifico anticipato</span>
								<span class="ph-payment-option__desc">Paga direttamente sul nostro conto bancario. Usa il numero d’ordine come causale. L’ordine verrà spedito solo dopo la ricezione del pagamento.</span>
							</span>
						</label>

						<label class="ph-checkout__note-toggle">
							<input type="checkbox" name="add_note" value="1" {{ old('add_note', old('notes') ? '1' : '') ? 'checked' : '' }} data-toggle-notes>
							<span>Aggiungi una nota al tuo ordine</span>
						</label>
						<div class="ph-checkout__notes" data-notes-wrap @if(! old('notes') && ! old('add_note')) hidden @endif>
							<textarea name="notes" rows="4" placeholder="Note sul tuo ordine">{{ old('notes') }}</textarea>
						</div>

						<p class="ph-checkout__legal">
							Continuando, accetti i nostri
							<a href="{{ url('/termini-e-condizioni/') }}">Termini e condizioni</a>
							e l’
							<a href="{{ url('/privacy/') }}">Informativa sulla privacy</a>.
						</p>

						<button type="submit" class="ph-checkout__submit">Ordina con obbligo di pagamento</button>
					</section>
				</div>

				<aside class="ph-checkout__summary">
					<div class="ph-checkout__summary-card">
						<h2 class="ph-checkout__summary-title">Riepilogo ordine</h2>
						<ul class="ph-checkout__items">
							@foreach ($cart as $productId => $qty)
								@continue(! isset($products[$productId]))
								@php($product = $products[$productId])
								@php($lineTotal = $product->price * $qty)
								@php($saved = $product->isOnSale() ? ($product->regular_price - $product->price) * $qty : 0)
								<li class="ph-checkout__item">
									<div class="ph-checkout__item-thumb">
										<img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" width="64" height="64" loading="lazy">
										<span class="ph-checkout__item-qty">{{ $qty }}</span>
									</div>
									<div class="ph-checkout__item-info">
										<p class="ph-checkout__item-name">{{ $product->name }}</p>
										<p class="ph-checkout__item-price">
											@if($product->isOnSale())
												<del>€{{ number_format($product->regular_price, 2) }}</del>
												<span>€{{ number_format($product->price, 2) }}</span>
												@if($saved > 0)
													<span class="ph-checkout__save">Risparmi €{{ number_format($saved, 2) }}</span>
												@endif
											@else
												<span>€{{ number_format($product->price, 2) }}</span>
											@endif
										</p>
									</div>
									<div class="ph-checkout__item-total">€{{ number_format($lineTotal, 2) }}</div>
								</li>
							@endforeach
						</ul>

						<div class="ph-checkout__totals">
							<div class="ph-checkout__total-row">
								<span>Subtotale</span>
								<span>€{{ number_format($total, 2) }}</span>
							</div>
							<div class="ph-checkout__total-row">
								<span>Spedizione in Italia</span>
								<span>Gratuita</span>
							</div>
							<div class="ph-checkout__total-row ph-checkout__total-row--grand">
								<span>Totale IVA inclusa</span>
								<span>€{{ number_format($total, 2) }}</span>
							</div>
						</div>
						@include('partials.purchase-terms')
					</div>
				</aside>
			</div>
		</form>
	</div>
</div>
@endsection
