<?php

namespace App\Http\Controllers;

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

        return view('page', ['page' => $page]);
    }
}
