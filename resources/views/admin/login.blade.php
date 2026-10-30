@extends('layouts.admin')

@section('title', 'Connexion administrateur')

@section('content')
<form method="POST" action="{{ route('admin.login.submit') }}" class="admin-card admin-card--narrow">
	@csrf
	<p class="admin-kicker">Rizzo Christian</p>
	<h1>Espace administrateur</h1>
	<p class="admin-lead">Modification des textes de confirmation et de paiement.</p>

	@if ($errors->any())
		<div class="admin-alert admin-alert--error">
			@foreach ($errors->all() as $error)
				<p>{{ $error }}</p>
			@endforeach
		</div>
	@endif

	<label class="admin-field">
		<span>E-mail</span>
		<input type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
	</label>

	<label class="admin-field">
		<span>Mot de passe</span>
		<input type="password" name="password" required autocomplete="current-password">
	</label>

	<button type="submit" class="admin-button">Entrer</button>
</form>
@endsection
