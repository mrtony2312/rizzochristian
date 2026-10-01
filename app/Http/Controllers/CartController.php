<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        return view('cart', compact('cart', 'products'));
    }

    public function add(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        if (! $product->in_stock) {
            $message = 'Questo articolo non è attualmente disponibile.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $qty = max(1, (int) $request->input('quantity', 1));
        $cart = session('cart', []);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $qty;
        session(['cart' => $cart]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => array_sum($cart),
                'product_name' => $product->name,
                'product_image' => $product->imageUrl(),
                'mini_cart_html' => view('partials.mini-cart')->render(),
            ]);
        }

        return back()->with('success', "«{$product->name}» è stato aggiunto al carrello.");
    }

    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $qty = max(0, (int) $request->input('quantity', 1));
        $cart = session('cart', []);

        if ($qty <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = $qty;
        }

        session(['cart' => $cart]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => array_sum($cart),
                'mini_cart_html' => view('partials.mini-cart')->render(),
            ]);
        }

        return back();
    }

    public function remove(Product $product, Request $request): RedirectResponse|JsonResponse
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => array_sum($cart),
                'mini_cart_html' => view('partials.mini-cart')->render(),
            ]);
        }

        return back();
    }
}
