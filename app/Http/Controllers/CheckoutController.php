<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderAdminNotification;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Il tuo carrello è vuoto.');
        }

        $products = Product::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');

        if ($products->isEmpty()) {
            session()->forget('cart');

            return redirect()->route('cart')->with('error', 'Il tuo carrello è vuoto.');
        }

        $total = $this->cartTotal($cart, $products);

        return view('checkout', compact('cart', 'products', 'total'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Il tuo carrello è vuoto.');
        }

        $provinceCodes = array_keys(config('billing.provinces', []));

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', Rule::in(array_keys(config('billing.countries', [])))],
            'address' => ['required', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', Rule::in($provinceCodes)],
            'phone' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', Rule::in(['bonifico'])],
            'add_note' => ['nullable'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $products = Product::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');

        foreach ($cart as $productId => $qty) {
            $product = $products->get((int) $productId) ?? $products->get($productId);
            if (! $product || ! $product->in_stock) {
                return back()->withInput()->with('error', 'Un articolo del carrello non è più disponibile.');
            }
        }

        $total = $this->cartTotal($cart, $products);

        try {
            $order = DB::transaction(function () use ($data, $cart, $products, $total) {
                $fullName = trim($data['first_name'].' '.$data['last_name']);

                do {
                    $reference = (string) random_int(10000, 99999);
                } while (Order::where('reference', $reference)->exists());

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'reference' => $reference,
                    'name' => $fullName,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'company' => $data['company'] ?? null,
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'country' => $data['country'],
                    'address' => $data['address'],
                    'address_2' => $data['address_2'] ?? null,
                    'city' => $data['city'],
                    'state' => $data['state'] ?? null,
                    'postal_code' => $data['postal_code'],
                    'notes' => ! empty($data['add_note']) ? ($data['notes'] ?? null) : null,
                    'payment_method' => $data['payment_method'],
                    'total' => $total,
                    'status' => 'pending',
                ]);

                foreach ($cart as $productId => $qty) {
                    $product = $products->get((int) $productId) ?? $products->get($productId);
                    if (! $product) {
                        continue;
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => max(1, (int) $qty),
                        'unit_price' => $product->price,
                    ]);
                }

                return $order;
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Impossibile completare l’ordine. Riprova tra poco.');
        }

        $order->load('items');
        session()->forget('cart');

        $orderId = $order->id;
        dispatch(function () use ($orderId) {
            $order = Order::query()->with('items')->find($orderId);
            if (! $order) {
                return;
            }

            try {
                $adminAddress = config('mail.admin_address');
                if ($adminAddress) {
                    Mail::to($adminAddress)->send(new NewOrderAdminNotification($order));
                }
                Mail::to($order->email)->send(new OrderConfirmation($order));
            } catch (\Throwable $e) {
                report($e);
            }
        })->afterResponse();

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order): View
    {
        $order->load('items');

        return view('checkout-success', compact('order'));
    }

    /**
     * @param  array<int|string, mixed>  $cart
     * @param  Collection<int|string, Product>  $products
     */
    private function cartTotal(array $cart, $products): float
    {
        $total = 0.0;

        foreach ($cart as $productId => $qty) {
            $product = $products->get((int) $productId) ?? $products->get($productId);
            if (! $product) {
                continue;
            }

            $total += ((float) $product->price) * max(1, (int) $qty);
        }

        return round($total, 2);
    }
}
