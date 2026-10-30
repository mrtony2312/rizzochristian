<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>@yield('title', 'Administration') — Rizzo Christian</title>
	<link rel="stylesheet" href="{{ asset('css/admin.css') }}?ver=2">
</head>
<body>
	<div class="admin-shell">
		@yield('content')
	</div>
</body>
</html>
