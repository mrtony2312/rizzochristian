<?php

namespace App\Http\Controllers;

use App\Mail\ContactAdminNotification;
use App\Mail\ContactConfirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contact = [
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
        ];

        try {
            $adminAddress = config('mail.admin_address');
            if ($adminAddress) {
                Mail::to($adminAddress)->send(new ContactAdminNotification($contact));
            }

            Mail::to($contact['email'])->send(new ContactConfirmation($contact));
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['email' => 'Il messaggio non può essere inviato al momento. Riprova più tardi.']);
        }

        return back()->with('success', 'Grazie! La tua richiesta è stata inviata. Riceverai a breve una conferma via e-mail.');
    }
}
