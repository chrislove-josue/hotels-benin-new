@extends('layouts.app')

@section('title', 'Connexion — Hôtels Bénin')

@section('content')
<section class="relative flex min-h-[75vh] items-center overflow-hidden bg-gradient-to-br from-hb-blue to-hb-blue-deeper px-4 py-16 sm:px-6">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-hb-orange/20 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-md" data-reveal>
        <div class="card w-full p-8">
            <div class="mb-6 text-center">
                <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-hb-blue to-hb-blue-deeper font-display text-xl font-bold text-hb-gold shadow-md">HB</span>
                <h1 class="font-display text-2xl font-bold text-hb-blue-deeper">Connexion</h1>
                <p class="mt-1 text-sm text-gray-500">Accédez à votre espace voyageur ou hôte.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input-field" required>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Mot de passe</label>
                    <input type="password" name="password" class="input-field" required>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-gray-600">
                        <input type="checkbox" class="rounded border-gray-300 text-hb-blue focus:ring-hb-blue">
                        Se souvenir de moi
                    </label>
                    <a href="#" class="font-semibold text-hb-orange">Mot de passe oublié ?</a>
                </div>
<button type="submit" class="btn-primary w-full"><i class="bi bi-box-arrow-in-right"></i> Se connecter</button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Pas encore de compte ?
                <a href="{{ route('register.show') }}" class="font-semibold text-hb-blue">S'inscrire</a>
            </p>
        </div>
    </div>
</section>
@endsection
