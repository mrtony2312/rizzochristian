@extends('layouts.app')

@section('title', 'Registrati - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta no-sidebar elementor-default elementor-kit-8')

@section('content')
<div id="page-header" class="page-header">
	<div class="container clearfix">
		<div class="page-header__content">
			<h1 class="page-header__title">Crea un account</h1>
		</div>
	</div>
</div>

<div id="site-content" class="site-content">
	<div class="container clearfix" style="max-width:480px;padding:40px 0;">

		@if ($errors->any())
			<div class="woocommerce-error" style="margin-bottom:20px;">
				<ul style="margin:0;">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form method="POST" action="{{ route('register') }}" class="motta-auth-form">
			@csrf
			<p class="form-row">
				<label for="register-name">Nome <span class="required">*</span></label>
				<input type="text" id="register-name" name="name" value="{{ old('name') }}" required class="motta-input--base" style="width:100%;">
			</p>
			<p class="form-row">
				<label for="register-email">Indirizzo e-mail <span class="required">*</span></label>
				<input type="email" id="register-email" name="email" value="{{ old('email') }}" required class="motta-input--base" style="width:100%;">
			</p>
			<p class="form-row">
				<label for="register-password">Password <span class="required">*</span></label>
				<input type="password" id="register-password" name="password" required class="motta-input--base" style="width:100%;">
			</p>
			<p class="form-row">
				<label for="register-password-confirm">Conferma password <span class="required">*</span></label>
				<input type="password" id="register-password-confirm" name="password_confirmation" required class="motta-input--base" style="width:100%;">
			</p>
			<button type="submit" class="button alt motta-button--bg-color-black">Registrati</button>
		</form>

		<p style="margin-top:20px;">Hai già un account? <a href="{{ route('login') }}">Accedi ora</a></p>
	</div>
</div>
@endsection
