@component('mail::message')
# Thank you for your order!

Hi {{ $order->name }},

Your order **{{ $order->order_number }}** has been successfully placed.

**Order Details:**

| Item               | Quantity | Price  |
|--------------------|----------|--------|
@foreach($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }}      | ${{ number_format($item->price, 2) }} |
@endforeach

**Total:** ${{ number_format($order->grand_total, 2) }}

**Shipping Address:**

{{ $order->address }}  
{{ $order->city }}, {{ $order->postal_code }}

If you have any questions, feel free to reply to this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
