<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $favorites = collect();

        if (Auth::check()) {
            $favorites = Favorite::with('product.category')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        return view('wishlist', compact('favorites'));
    }

    public function renderGuest(Request $request): JsonResponse
    {
        $ids = array_filter(array_map('intval', explode(',', (string) $request->query('ids', ''))));

        $products = Product::with('category')->whereIn('id', $ids)->get();

        return response()->json([
            'html' => $products->isEmpty() ? null : view('partials.product-grid', compact('products'))->render(),
        ]);
    }

    public function toggle(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        if (! Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'redirect' => route('login')], 401);
            }

            return redirect()->route('login');
        }

        $favorite = Favorite::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($favorite) {
            $favorite->delete();
            $added = false;
        } else {
            Favorite::create(['user_id' => Auth::id(), 'product_id' => $product->id]);
            $added = true;
        }

        $count = Favorite::where('user_id', Auth::id())->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'added' => $added, 'count' => $count]);
        }

        return back();
    }
}
