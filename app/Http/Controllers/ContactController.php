<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ], [], [
            'name' => 'nom',
            'email' => 'e-mail',
            'subject' => 'sujet',
            'message' => 'message',
        ]);

        $contact = ContactMessage::create($data);

        // Envoi de l'e-mail au destinataire configuré (voir config/mail.php et .env).
        // Le Mailable ContactMessageMail rend la vue emails.contact-message.
        try {
            Mail::to(env('CONTACT_TO_ADDRESS', config('mail.from.address')))
                ->send(new \App\Mail\ContactMessageMail($contact));
        } catch (\Throwable $e) {
            report($e);
            // On n'empêche pas la confirmation utilisateur si le SMTP n'est pas configuré en local.
        }

        return back()->with('status', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
    }
}
