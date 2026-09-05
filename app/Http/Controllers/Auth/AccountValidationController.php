<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountValidationController extends Controller
{
    public function validate(string $token): RedirectResponse
    {
        $loginToken = LoginToken::where('token', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->first();

        if (! $loginToken) {
            return redirect()->route('login.show')
                ->withErrors(['email' => 'Ce lien de validation est invalide ou a expiré. Veuillez vous connecter avec vos identifiants.']);
        }

        $user = $loginToken->user;

        // Le lien valide le compte : approbation + e-mail vérifié.
        $user->update([
            'is_approved' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        $loginToken->delete();

        Auth::login($user);

        $request = app(Request::class);
        $request->session()->regenerate();

        return redirect($this->homeFor($user));
    }

    protected function homeFor(User $user): string
    {
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }
        if ($user->isHote()) {
            return route('hote.dashboard');
        }

        return route('reservations.dashboard');
    }
}