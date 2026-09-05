<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthPageController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
$credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email' => 'e-mail',
            'password' => 'mot de passe',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Compte non approuvé par un administrateur : connexion bloquée.
            if ($user->isPending()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Votre compte est en attente d\'approbation par un administrateur. Veuillez réessayer plus tard.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended($this->homeFor($user));
        }

        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
$data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email', new \App\Rules\ValidRealEmail],
            'phone' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9\s\-\.]{8,30}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:voyageur,hote'],
        ], [], [
            'name' => 'nom',
            'email' => 'e-mail',
            'phone' => 'numéro de téléphone / WhatsApp',
            'password' => 'mot de passe',
            'role' => 'profil',
        ]);

$user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            // Nouveau compte : en attente d'approbation par un administrateur.
            'is_approved' => false,
        ]);

        // E-mail d'accueil avec les identifiants et le lien de validation/connexion directe.
        $sent = $user->sendAccountCreatedNotification($data['password']);

        // On ne connecte pas automatiquement : l'utilisateur valide son compte via le lien reçu par e-mail.
        return redirect()->route('register.pending')->with('mail_sent', $sent);
    }

    /**
     * Page intermédiaire affichée après l'inscription : compte en attente d'approbation.
     */
    public function pending()
    {
        return view('auth.pending');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function homeFor(User $user): string
    {
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }
        if ($user->isHote()) {
            return route('hote.dashboard');
        }

        // Voyageur : directement son tableau de bord.
        return route('reservations.dashboard');
    }
}
