@php($sections = $product->descriptionSections())

@foreach($sections as $heading => $lines)
	<h2>{{ $heading }}</h2>

	@if($heading === 'Riepilogo tecnico' || $heading === 'Stoccaggio e utilizzo' || $heading === 'Conservazione e utilizzo')
		<ul>
			@foreach($lines as $line)
				<li>{{ $line }}</li>
			@endforeach
		</ul>

	@elseif($heading === 'Caratteristiche tecniche')
		<table class="product-spec-table" style="width:100%;border-collapse:collapse;">
			@for($i = 0; $i < count($lines); $i += 2)
				<tr style="border-bottom:1px solid #eee;">
					<td style="padding:10px 0;color:#666;width:40%;">{{ $lines[$i] }}</td>
					<td style="padding:10px 0;">{{ $lines[$i + 1] ?? '' }}</td>
				</tr>
			@endfor
		</table>

	@elseif($heading === 'Consegna')
		@php($intro = str_contains($lines[0] ?? '', ':') ? array_shift($lines) : null)
		@if($intro)
			<p><strong>{{ Str::before($intro, ':') }}:</strong>{{ Str::after($intro, ':') }}</p>
		@endif
		@if(count($lines))
			<ul>
				@foreach($lines as $line)
					<li>{{ $line }}</li>
				@endforeach
			</ul>
		@endif

	@else
		@foreach($lines as $line)
			<p>{{ $line }}</p>
		@endforeach
	@endif
@endforeach
