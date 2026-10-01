<x-mail::message>
# Grazie per il tuo ordine, {{ $order->first_name ?: $order->name }}!

Il tuo ordine **#{{ $order->reference }}** è stato registrato con successo e verrà ora elaborato.

@if($order->isBankTransfer())
Ti preghiamo di bonificare l’importo totale e di inviarci una copia del bonifico a [{{ config('mail.admin_address') }}](mailto:{{ config('mail.admin_address') }}).
@endif

## Riepilogo ordine

<x-mail::table>
| Prodotto | Quantità | Prezzo |
| :------ | :---: | ----: |
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | {{ number_format((float) $item->unit_price, 2, ',', '.') }} € |
@endforeach
| **Totale** |  | **{{ number_format((float) $order->total, 2, ',', '.') }} €** |
</x-mail::table>

<x-mail::panel>
**Indirizzo di consegna**<br>
{{ $order->name }}<br>
{{ $order->address }}@if($order->address_2), {{ $order->address_2 }}@endif<br>
{{ $order->postal_code }} {{ $order->city }}@if($order->provinceName()), {{ $order->provinceName() }}@endif<br>
{{ $order->countryName() }}<br>
@if($order->phone)
Tel.: {{ $order->phone }}
@endif
</x-mail::panel>

<x-mail::button :url="route('tracking-order', ['order_id' => $order->reference, 'email' => $order->email])" color="primary">
Traccia ordine
</x-mail::button>

Per domande sul tuo ordine scrivici in qualsiasi momento a [{{ config('mail.admin_address') }}](mailto:{{ config('mail.admin_address') }}).

Cordiali saluti,<br>
Il team Rizzo Christian
</x-mail::message>
