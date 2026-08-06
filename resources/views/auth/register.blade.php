@extends('layouts.app')

@section('title', 'Inscription — Hôtels Bénin')

@section('content')
<section class="relative flex min-h-[75vh] items-center overflow-hidden bg-gradient-to-br from-hb-blue to-hb-blue-deeper px-4 py-16 sm:px-6">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-hb-orange/20 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-md" data-reveal>
        <div class="card w-full p-8">
            <div class="mb-6 text-center">
                <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-hb-blue to-hb-blue-deeper font-display text-xl font-bold text-hb-gold shadow-md">HB</span>
                <h1 class="font-display text-2xl font-bold text-hb-blue-deeper">Créer un compte</h1>
                <p class="mt-1 text-sm text-gray-500">Rejoignez la communauté Hôtels Bénin.</p>
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

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input-field" required>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input-field" required>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Téléphone</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" class="input-field" placeholder="+229 ...">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Vous êtes</label>
                    <select name="role" class="input-field">
                        <option value="voyageur">Voyageur — je cherche un hébergement</option>
                        <option value="hote">Hôte — je propose un établissement</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Mot de passe</label>
                        <input type="password" name="password" class="input-field" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Confirmation</label>
                        <input type="password" name="password_confirmation" class="input-field" required>
                    </div>
                </div>
<button type="submit" class="btn-primary w-full"><i class="bi bi-person-plus"></i> Créer mon compte</button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Déjà inscrit ?
                <a href="{{ route('login.show') }}" class="font-semibold text-hb-blue">Se connecter</a>
            </p>
        </div>
    </div>
</section>
@endsection
