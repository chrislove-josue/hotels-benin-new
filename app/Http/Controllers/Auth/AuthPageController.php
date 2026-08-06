<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Contrôleur "vitrine" pour les formulaires de connexion / inscription.
 * Conformément au cahier des charges, la partie publique affiche
 * uniquement les formulaires et une validation basique des champs :
 * aucune logique de session avancée n'est implémentée ici (le back-office
 * gèrera l'authentification réelle).
 */
class AuthPageController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ], [], [
            'email' => 'e-mail',
            'password' => 'mot de passe',
        ]);

        return back()->with('status', 'Formulaire validé. La connexion sera activée avec le back-office.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:voyageur,hote'],
        ], [], [
            'name' => 'nom',
            'email' => 'e-mail',
            'phone' => 'téléphone',
            'password' => 'mot de passe',
            'role' => 'profil',
        ]);

        return back()->with('status', 'Formulaire validé. Votre compte sera créé dès l\'activation du back-office.');
    }
}
