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
            $request->session()->regenerate();

            return redirect()->intended($this->homeFor(Auth::user()));
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
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
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

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isHote()) {
            return redirect()->route('hote.dashboard')->with('status', 'Bienvenue ! Votre espace hôte est prêt.');
        }

        return redirect()->route('reservations.dashboard')->with('status', 'Bienvenue ! Votre compte a été créé.');
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

        return route('home');
    }
}
