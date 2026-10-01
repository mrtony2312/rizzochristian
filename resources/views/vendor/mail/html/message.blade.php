<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
PelletCasa
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
**PelletCasa** · LEGNO &amp; PELLET<br>
<a href="mailto:{{ config('mail.admin_address') }}">{{ config('mail.admin_address') }}</a>
· <a href="tel:+390284751932">+39 02 8475 1932</a><br>
Via Monte Napoleone 18, 20121 Milano, Italia<br><br>
© {{ date('Y') }} PelletCasa. Tutti i diritti riservati.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
