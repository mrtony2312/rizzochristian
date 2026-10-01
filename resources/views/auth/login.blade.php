@extends('layouts.auth')

@section('title', 'Accedi - Rizzo Christian')

@section('content')
<div class="auth-card">
	<h1 class="auth-card__title">Accedi</h1>

	@if ($errors->any())
		<div class="auth-alert auth-alert--error">
			@foreach ($errors->all() as $error)
				<p>{{ $error }}</p>
			@endforeach
		</div>
	@endif

	@if (session('success'))
		<div class="auth-alert auth-alert--success">
			<p>{{ session('success') }}</p>
		</div>
	@endif

	<form method="POST" action="{{ route('login') }}" class="auth-form">
		@csrf
		<div class="auth-field">
			<label for="login-email">Nome utente o indirizzo e-mail</label>
			<input id="login-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
		</div>

		<div class="auth-field">
			<label for="login-password">Password</label>
			<div class="auth-password">
				<input id="login-password" type="password" name="password" required autocomplete="current-password">
				<button type="button" class="auth-password__toggle" data-password-toggle aria-label="Mostra password">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.1A9.8 9.8 0 0 1 12 5c5 0 9.3 3.1 11 7.5a12.3 12.3 0 0 1-4.2 5.1"/><path d="M6.1 6.1A12.4 12.4 0 0 0 1 12.5C2.7 16.9 7 20 12 20c1.4 0 2.7-.2 3.9-.7"/></svg>
				</button>
			</div>
		</div>

		<div class="auth-form__meta">
			<label class="auth-checkbox">
				<input type="checkbox" name="remember" value="1" @checked(old('remember'))>
				<span>Mantieni l’accesso</span>
			</label>
			<a href="{{ route('login') }}" class="auth-link">Password dimenticata?</a>
		</div>

		<button type="submit" class="auth-submit">Accedi</button>
	</form>
</div>

<script>
document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
	btn.addEventListener('click', function () {
		var input = btn.parentElement.querySelector('input');
		if (!input) return;
		input.type = input.type === 'password' ? 'text' : 'password';
	});
});
</script>
@endsection
