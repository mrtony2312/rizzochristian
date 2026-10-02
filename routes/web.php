<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MerchantFeedController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('feed/google-merchant.xml', [MerchantFeedController::class, 'google'])->name('merchant.feed');
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('negozio/', [ShopController::class, 'shop'])->name('shop');
Route::get('categoria-prodotto/{slug}/', [ShopController::class, 'category'])->name('category');
Route::get('prodotto/{slug}/', [ShopController::class, 'product'])->name('product');
Route::get('prodotto/{slug}/anteprima', [ShopController::class, 'quickview'])->name('product.quickview');

Route::get('carrello/', [CartController::class, 'index'])->name('cart');
Route::post('carrello/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('carrello/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('carrello/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('cassa/', [CheckoutController::class, 'index'])->name('checkout');
Route::post('cassa/', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('ordine-confermato/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('login/', [AuthController::class, 'showLogin'])->name('login');
Route::post('login/', [AuthController::class, 'login']);
Route::get('register/', [AuthController::class, 'showRegister'])->name('register');
Route::post('register/', [AuthController::class, 'register']);
Route::post('logout/', [AuthController::class, 'logout'])->name('logout');
Route::get('il-mio-account/', [AuthController::class, 'account'])->name('account');

Route::get('lista-desideri/', [FavoriteController::class, 'index'])->name('wishlist');
Route::get('lista-desideri/render', [FavoriteController::class, 'renderGuest'])->name('wishlist.render');
Route::post('lista-desideri/toggle/{product}', [FavoriteController::class, 'toggle'])->name('wishlist.toggle');

Route::get('traccia-ordine/', [OrderTrackingController::class, 'show'])->name('tracking-order');
Route::get('centro-assistenza/', fn () => view('pages.help-center'))->name('help-center');
Route::get('contattaci/', [ContactController::class, 'show'])->name('contact');
Route::post('contattaci/', [ContactController::class, 'store'])->name('contact.store');

$staticPages = [
    'termini-e-condizioni', 'resi-e-rimborsi', 'spedizione-e-consegna',
    'privacy', 'note-legali', 'metodi-di-pagamento', 'chi-siamo',
];

Route::get('{slug}/', [PageController::class, 'show'])
    ->where('slug', implode('|', $staticPages))
    ->name('page');
