<?php

namespace App\Http\Controllers;

use App\Models\SiteText;
use App\Support\SiteTexts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminContentController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::user()?->is_admin) {
            return redirect()->route('admin.edit');
        }

        return view('admin.login', [
            'locales' => config('site_texts.locales', []),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Ces identifiants ne sont pas valides.',
            ]);
        }

        if (! Auth::user()?->is_admin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Cet accès est réservé à l’administrateur.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.edit'));
    }

    public function edit(Request $request): View
    {
        $locales = config('site_texts.locales', []);
        $locale = (string) $request->query('locale', SiteTexts::locale());

        if (! array_key_exists($locale, $locales)) {
            $locale = SiteTexts::locale();
        }

        $values = [];
        foreach (array_keys(config('site_texts.fields', [])) as $key) {
            $values[$key] = SiteTexts::text($key, $locale);
        }

        return view('admin.edit', [
            'locales' => $locales,
            'locale' => $locale,
            'groups' => config('site_texts.groups', []),
            'fields' => config('site_texts.fields', []),
            'values' => $values,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $locales = array_keys(config('site_texts.locales', []));
        $fields = config('site_texts.fields', []);

        $data = $request->validate([
            'locale' => ['required', 'string', Rule::in($locales)],
            'values' => ['required', 'array'],
            'values.*.key' => ['required', 'string', Rule::in(array_keys($fields))],
            'values.*.value' => ['nullable', 'string', 'max:2000'],
        ]);

        $errors = [];

        foreach ($data['values'] as $index => $row) {
            $key = $row['key'];
            $value = trim((string) ($row['value'] ?? ''));
            $type = $fields[$key]['type'] ?? 'text';

            if ($type === 'checkbox') {
                $data['values'][$index]['value'] = $value === '1' ? '1' : '0';

                continue;
            }

            if ($type === 'email' && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors["values.$index.value"] = 'Indiquez une adresse e-mail valide.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        foreach ($data['values'] as $row) {
            SiteText::query()->updateOrCreate(
                ['locale' => $data['locale'], 'key' => $row['key']],
                ['value' => trim((string) ($row['value'] ?? ''))],
            );
        }

        SiteTexts::flush($data['locale']);

        return redirect()
            ->route('admin.edit', ['locale' => $data['locale']])
            ->with('success', 'Textes enregistrés.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
