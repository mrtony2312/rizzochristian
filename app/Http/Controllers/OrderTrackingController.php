<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function show(Request $request): View
    {
        $order = null;
        $notFound = false;

        if ($request->filled(['order_id', 'email'])) {
            $data = $request->validate([
                'order_id' => ['required', 'string', 'max:64'],
                'email' => ['required', 'email'],
            ]);

            $order = Order::query()
                ->with('items.product')
                ->where('reference', $data['order_id'])
                ->where('email', $data['email'])
                ->first();

            $notFound = $order === null;
        }

        return view('pages.tracking-order', [
            'order' => $order,
            'notFound' => $notFound,
            'orderId' => $request->input('order_id', ''),
            'email' => $request->input('email', ''),
        ]);
    }
}
