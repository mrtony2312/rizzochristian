<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function home(): View
    {
        $categories = Category::orderBy('name')->get();
        $featured = Product::with('category')->latest()->take(8)->get();

        $byCategory = fn (string $slug) => Product::with('category')
            ->whereHas('category', fn ($q) => $q->where('slug', $slug))
            ->latest()->take(8)->get();

        $pelletofenProducts = $byCategory('stufe-a-pellet');
        $holzpelletsProducts = $byCategory('pellet-di-legno');
        $brennholzProducts = $byCategory('legna-da-ardere');

        return view('home', compact(
            'categories', 'featured',
            'pelletofenProducts', 'holzpelletsProducts', 'brennholzProducts'
        ));
    }

    public function shop(Request $request): View|JsonResponse
    {
        $query = Product::with('category');

        $search = trim((string) ($request->input('s') ?: $request->input('search') ?: ''));
        if ($search !== '') {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $rawCats = $request->input('product_cat', []);
        if (! is_array($rawCats)) {
            $rawCats = [$rawCats];
        }
        $selectedCategories = array_values(array_filter(
            array_map('strval', $rawCats),
            fn (string $slug) => $slug !== '' && $slug !== '0'
        ));
        if ($selectedCategories) {
            $query->whereHas('category', fn ($q) => $q->whereIn('slug', $selectedCategories));
        }

        $this->applyPriceAndSort($query, $request);

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->orderBy('name')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return $this->catalogAjaxResponse($products, null);
        }

        return view('shop', compact('products', 'categories', 'selectedCategories'));
    }

    public function category(Request $request, string $slug): View|JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = Product::with('category')->where('category_id', $category->id);
        $this->applyPriceAndSort($query, $request);

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->orderBy('name')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return $this->catalogAjaxResponse($products, $category);
        }

        return view('category', compact('category', 'products', 'categories'));
    }

    protected function catalogAjaxResponse($products, ?Category $category): JsonResponse
    {
        $total = $products->total();

        return response()->json([
            'success' => true,
            'count_html' => $total > 0
                ? $total.' risultati'
                : '0 risultati',
            'products_html' => view('partials.catalog-products', compact('products'))->render(),
            'activated_html' => view('partials.catalog-activated-filters', [
                'category' => $category,
                'categories' => Category::withCount('products')->orderBy('name')->get(),
                'selectedCategories' => $category ? [] : array_values(array_filter(
                    array_map('strval', (array) request('product_cat', [])),
                    fn (string $slug) => $slug !== '' && $slug !== '0'
                )),
            ])->render(),
            'total' => $total,
        ]);
    }

    protected function applyPriceAndSort($query, Request $request): void
    {
        $ranges = array_values(array_filter((array) $request->input('price_range', [])));

        if ($ranges) {
            $query->where(function ($q) use ($ranges) {
                foreach ($ranges as $range) {
                    [$min, $max] = array_pad(explode('-', (string) $range), 2, null);
                    $q->orWhere(function ($sub) use ($min, $max) {
                        if ($min !== null && $min !== '') {
                            $sub->where('price', '>=', (float) $min);
                        }
                        if ($max !== null && $max !== '') {
                            $sub->where('price', '<=', (float) $max);
                        }
                    });
                }
            });
        } else {
            if ($request->filled('min_price')) {
                $query->where('price', '>=', (float) $request->input('min_price'));
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', (float) $request->input('max_price'));
            }
        }

        match ($request->input('orderby')) {
            'price' => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            'date' => $query->latest(),
            default => $query->orderBy('name'),
        };
    }

    public function product(string $slug): View|RedirectResponse
    {
        if ($redirect = $this->redirectLegacyProductSlug($slug, 'product')) {
            return $redirect;
        }

        $product = Product::with(['category', 'images'])->where('slug', $slug)->firstOrFail();
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)->take(4)->get();

        return view('product', compact('product', 'related'));
    }

    public function quickview(string $slug): View|RedirectResponse
    {
        if ($redirect = $this->redirectLegacyProductSlug($slug, 'product.quickview')) {
            return $redirect;
        }

        $product = Product::with('category')->where('slug', $slug)->firstOrFail();

        return view('partials.quick-view', compact('product'));
    }

    private function redirectLegacyProductSlug(string $slug, string $route): ?RedirectResponse
    {
        $target = [
            'pellet-di-qualita-primex-premium-990-kg' => 'pellet-di-qualita-primex-premium-975-kg',
        ][$slug] ?? null;

        if ($target === null) {
            return null;
        }

        return redirect()->route($route, $target, 301);
    }
}
