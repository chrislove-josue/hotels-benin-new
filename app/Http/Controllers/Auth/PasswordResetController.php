<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function requestForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [], [
            'email' => 'e-mail',
        ]);

        // Vérification explicite de l'existence du compte.
        if (! User::where('email', $request->input('email'))->exists()) {
            return back()->withErrors([
                'email' => 'Aucun compte ne correspond à cette adresse e-mail.',
            ])->onlyInput('email');
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Un lien de réinitialisation vient de vous être envoyé par e-mail. Vérifiez votre boîte de réception (et vos spams).');
        }

        return back()->withErrors([
            'email' => $this->messageFor($status),
        ])->onlyInput('email');
    }

    public function resetForm(Request $request, string $token)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'email' => 'e-mail',
            'password' => 'mot de passe',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Pas de connexion automatique : même parcours que la connexion classique
            // (les comptes en attente d'approbation restent bloqués à ce stade).
            return redirect()->route('login.show')
                ->with('status', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
        }

        return back()->withErrors([
            'email' => $this->messageFor($status),
        ])->onlyInput('email');
    }

    protected function messageFor(string $status): string
    {
        return match ($status) {
            Password::INVALID_USER => 'Aucun compte ne correspond à cette adresse e-mail.',
            Password::INVALID_TOKEN => 'Ce lien de réinitialisation est invalide ou a expiré. Veuillez refaire une demande.',
            Password::RESET_THROTTLED => 'Veuillez patienter une minute avant de faire une nouvelle demande.',
            default => 'Une erreur est survenue. Veuillez réessayer.',
        };
    }
}
