<?php

namespace App\Http\Controllers;

use App\Support\SiteTexts;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class PageController extends Controller
{
    protected function pages(): array
    {
        static $pages;

        return $pages ??= collect(
            json_decode(File::get(database_path('data/pages.json')), true)
        )->keyBy('slug')->all();
    }

    public function show(string $slug): View
    {
        $page = $this->pages()[$slug] ?? abort(404);

        if ($slug === 'metodi-di-pagamento') {
            $page['content'] = str_replace(
                '<!--site-text:payment.exclusive-->',
                '<p>'.e(SiteTexts::text('payment.exclusive')).'</p>',
                $page['content']
            );
            $page['content'] = str_replace(
                '<!--bank-transfer-instructions-->',
                view('partials.bank-transfer-instructions', ['inline' => true])->render(),
                $page['content']
            );
        }

        return view('page', ['page' => $page]);
    }
}
