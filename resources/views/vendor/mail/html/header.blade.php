@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@php
    $logoPath = public_path('images/logo-rizzo.png');
    $logoSrc = asset('images/logo-rizzo.png');

    if (is_file($logoPath) && isset($message)) {
        $logoSrc = $message->embed($logoPath);
    }
@endphp
<img src="{{ $logoSrc }}" width="140" height="140" class="logo" alt="Rizzo Christian" style="width:140px;height:auto;border:0;display:block;">
</a>
</td>
</tr>
