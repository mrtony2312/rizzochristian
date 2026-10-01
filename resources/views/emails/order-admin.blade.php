<x-mail::message>
# Nuovo ordine #{{ $order->reference }}

Un nuovo ordine è stato appena effettuato su **Rizzo Christian**.

<x-mail::panel>
**Cliente:** {{ $order->name }} ([{{ $order->email }}](mailto:{{ $order->email }}))  
**Telefono:** {{ $order->phone ?: '—' }}  
**Pagamento:** {{ $order->paymentLabel() }}  
**Stato:** {{ $order->statusLabel() }}  
**Indirizzo di consegna:** {{ $order->address }}@if($order->address_2), {{ $order->address_2 }}@endif, {{ $order->postal_code }} {{ $order->city }}@if($order->provinceName()), {{ $order->provinceName() }}@endif, {{ $order->countryName() }}
@if($order->notes)

**Note:** {{ $order->notes }}
@endif
</x-mail::panel>

## Articoli

<x-mail::table>
| Prodotto | Quantità | Prezzo |
| :------ | :---: | ----: |
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | {{ number_format((float) $item->unit_price, 2, ',', '.') }} € |
@endforeach
| **Totale** |  | **{{ number_format((float) $order->total, 2, ',', '.') }} €** |
</x-mail::table>

<x-mail::button :url="'mailto:'.$order->email" color="primary">
Contatta il cliente
</x-mail::button>

Cordiali saluti,<br>
Sistema Rizzo Christian
</x-mail::message>
