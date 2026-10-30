@extends('layouts.admin')

@section('title', 'Textes du site')

@section('content')
<form method="POST" action="{{ route('admin.update') }}" class="admin-card">
	@csrf
	<div class="admin-toolbar">
		<div>
			<p class="admin-kicker">Rizzo Christian</p>
			<h1>Textes de paiement</h1>
		</div>
		<div class="admin-toolbar__actions">
			<button type="submit" class="admin-button">Enregistrer</button>
			<button type="submit" class="admin-button admin-button--quiet" form="admin-logout">Quitter</button>
		</div>
	</div>

	@if (session('success'))
		<div class="admin-alert admin-alert--success">
			<p>{{ session('success') }}</p>
		</div>
	@endif

	@if ($errors->any())
		<div class="admin-alert admin-alert--error">
			@foreach ($errors->all() as $error)
				<p>{{ $error }}</p>
			@endforeach
		</div>
	@endif

	<label class="admin-field">
		<span>Langue affichée sur le site</span>
		<select onchange="window.location='{{ route('admin.edit') }}?locale=' + this.value">
			@foreach ($locales as $code => $label)
				<option value="{{ $code }}" @selected($locale === $code)>{{ $label }}</option>
			@endforeach
		</select>
	</label>
	<p class="admin-hint">Le shop conclut les commandes en italien. Cette liste reprend les langues réellement disponibles sur le site.</p>
	<input type="hidden" name="locale" value="{{ $locale }}">

	@php($oldValues = old('values', []))
	@php($index = 0)
	@foreach ($groups as $group)
		<section class="admin-group">
			<h2>{{ $group['label'] }}</h2>
			@if (! empty($group['hint']))
				<p class="admin-hint">{{ $group['hint'] }}</p>
			@endif

			@foreach ($group['keys'] as $key)
				@php($field = $fields[$key])
				@php($current = $values[$key] ?? '')
				@foreach ($oldValues as $oldRow)
					@if (($oldRow['key'] ?? '') === $key)
						@php($current = $oldRow['value'] ?? $current)
					@endif
				@endforeach
				<label class="admin-field {{ ($field['type'] ?? '') === 'checkbox' ? 'admin-field--check' : '' }}">
					<input type="hidden" name="values[{{ $index }}][key]" value="{{ $key }}">
					@if (($field['type'] ?? 'text') === 'checkbox')
						<input type="hidden" name="values[{{ $index }}][value]" value="0">
						<input type="checkbox" name="values[{{ $index }}][value]" value="1" @checked((string) $current === '1')>
						<span>{{ $field['label'] }}</span>
					@elseif (($field['type'] ?? 'text') === 'textarea')
						<span>{{ $field['label'] }}</span>
						<textarea name="values[{{ $index }}][value]" rows="3">{{ $current }}</textarea>
					@else
						<span>{{ $field['label'] }}</span>
						<input
							type="{{ ($field['type'] ?? 'text') === 'email' ? 'email' : 'text' }}"
							name="values[{{ $index }}][value]"
							value="{{ $current }}"
							@if (($field['type'] ?? '') === 'email') required @endif
						>
					@endif
					@if (! empty($field['hint']))
						<small>{{ $field['hint'] }}</small>
					@endif
				</label>
				@php($index++)
			@endforeach
		</section>
	@endforeach

	<button type="submit" class="admin-button">Enregistrer</button>
</form>

<form id="admin-logout" method="POST" action="{{ route('admin.logout') }}">
	@csrf
</form>
@endsection
