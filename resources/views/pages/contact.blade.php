@extends('layouts.app')

@section('title', 'Contattaci - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta no-sidebar elementor-default elementor-kit-8')

@section('content')
<div id="site-content" class="site-content pages-shell">
	<div class="container clearfix">
		<section class="contact-page">
			<h1 class="contact-page__title">Contattaci</h1>

			<div class="contact-intro">
				<p><strong>Siamo qui per te</strong></p>
				<p>Hai domande sui nostri prodotti, sul tuo ordine, sulle condizioni di consegna oppure hai bisogno di una consulenza personalizzata?</p>
				<p>Il team di Rizzo Christian è a tua disposizione. Supportiamo clienti privati e aziende nella scelta di legna da ardere, pellet di legno, bricchetti di legno e altri combustibili di legno.</p>
				<p>Scrivici via e-mail o tramite il modulo di contatto. Il negozio è gestito da Rizzo Christian, impresa individuale. Il servizio clienti risponde alle tue richieste in modo rapido e affidabile.</p>
			</div>

			<div class="contact-info">
				<div class="contact-info__item">
					<span class="contact-info__icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5L4 8V6l8 5 8-5v2z"/></svg>
					</span>
					<a href="mailto:contatto@rizzochristian.com">contatto@rizzochristian.com</a>
				</div>
				<div class="contact-info__item">
					<span class="contact-info__icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M21.5 3.5 2.5 12l8.2 2.3L13 22.5l8.5-19z"/></svg>
					</span>
					<span>Viale Novaro 53A, 12078 Ormea (CN), Piemonte, Italia</span>
				</div>
				<div class="contact-info__item">
					<span>Partita IVA 00137368882 · IT00137368882</span>
				</div>
			</div>

			<div class="contact-form-wrap">
				<h2 class="contact-form-wrap__title">Modulo di contatto</h2>

				@if (session('success'))
					<div class="pages-alert pages-alert--success">{{ session('success') }}</div>
				@endif

				@if ($errors->any())
					<div class="pages-alert pages-alert--error">
						@foreach ($errors->all() as $error)
							<p>{{ $error }}</p>
						@endforeach
					</div>
				@endif

				<form method="POST" action="{{ route('contact.store') }}" class="contact-form">
					@csrf
					<div class="contact-field">
						<label for="contact-name">Il tuo nome</label>
						<input id="contact-name" type="text" name="name" value="{{ old('name') }}" required>
					</div>
					<div class="contact-field">
						<label for="contact-email">La tua e-mail</label>
						<input id="contact-email" type="email" name="email" value="{{ old('email') }}" required>
					</div>
					<div class="contact-field">
						<label for="contact-subject">Oggetto</label>
						<input id="contact-subject" type="text" name="subject" value="{{ old('subject') }}">
					</div>
					<div class="contact-field">
						<label for="contact-message">Il tuo messaggio</label>
						<textarea id="contact-message" name="message" rows="6" required>{{ old('message') }}</textarea>
					</div>
					<button type="submit" class="contact-submit">Invia la mia richiesta</button>
				</form>
			</div>
		</section>
	</div>
</div>
@endsection
