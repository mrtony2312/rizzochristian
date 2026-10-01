@php
    $whatsappNumber = preg_replace('/\D+/', '', (string) config('services.whatsapp.number', ''));
    $whatsappMessage = trim((string) config('services.whatsapp.message', ''));
    $whatsappUrl = $whatsappNumber !== ''
        ? 'https://wa.me/'.$whatsappNumber.($whatsappMessage !== '' ? '?text='.rawurlencode($whatsappMessage) : '')
        : route('contact');
@endphp
<a
	class="ph-whatsapp-button"
	href="{{ $whatsappUrl }}"
	@if ($whatsappNumber !== '')
		target="_blank"
		rel="noopener noreferrer"
	@endif
	aria-label="Scrivici su WhatsApp"
	title="WhatsApp"
>
	<svg viewBox="0 0 32 32" aria-hidden="true" focusable="false">
		<path fill="currentColor" d="M16.004 3.2c-7.04 0-12.8 5.664-12.8 12.64 0 2.24.64 4.416 1.824 6.304L3.2 28.8l6.848-1.792A12.72 12.72 0 0 0 16.004 28.48c7.04 0 12.8-5.664 12.8-12.64S23.044 3.2 16.004 3.2zm0 23.04c-2.016 0-3.968-.544-5.664-1.568l-.4-.24-4.064 1.056 1.088-3.968-.256-.416a10.24 10.24 0 0 1-1.6-5.504c0-5.664 4.672-10.272 10.4-10.272s10.4 4.608 10.4 10.272-4.672 10.64-10.904 10.64zm5.696-7.712c-.304-.16-1.824-.896-2.112-1-.288-.096-.496-.144-.704.16s-.8.992-.992 1.2-.368.24-.672.08c-.304-.16-1.28-.464-2.432-1.488-.896-.8-1.504-1.792-1.68-2.096-.176-.304-.019-.464.141-.624.144-.144.304-.368.448-.552.144-.184.192-.304.288-.512.096-.208.048-.384-.024-.544-.08-.16-.704-1.696-.976-2.32-.256-.608-.512-.528-.704-.528h-.608c-.208 0-.544.08-.832.384s-1.088 1.056-1.088 2.576 1.12 2.992 1.28 3.2c.16.208 2.176 3.328 5.28 4.656.736.32 1.312.512 1.76.656.736.232 1.408.2 1.936.12.592-.088 1.824-.736 2.08-1.456.256-.72.256-1.344.176-1.456-.08-.128-.272-.208-.576-.368z"/>
	</svg>
</a>
