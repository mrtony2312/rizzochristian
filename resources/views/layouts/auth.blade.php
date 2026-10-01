<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title', 'Rizzo Christian')</title>
	<link rel="stylesheet" href="{{ asset('css/pages.css') }}?ver=6">
</head>
<body class="auth-layout">
	<div class="auth-page">
		<a href="{{ route('home') }}" class="auth-logo" aria-label="Rizzo Christian">
			<img src="{{ asset('images/logo-rizzo.png') }}" alt="Rizzo Christian">
		</a>
		@yield('content')
	</div>
</body>
</html>
