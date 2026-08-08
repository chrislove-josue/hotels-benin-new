<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login.show');
        }

        if (Auth::user()->isAdmin()) {
            return $next($request);
        }

        if (Auth::user()->isPending()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.show')
                ->withErrors(['email' => 'Votre compte est en attente d\'approbation par un administrateur.']);
        }

        return $next($request);
    }
}
