@extends('layouts.backoffice')

@section('title', 'Nouvel utilisateur — Administration')

@section('content')
<div class="mb-6">
    <nav class="mb-4 text-sm text-gray-500">
        <a href="{{ route('admin.users') }}" class="hover:text-hb-orange">Utilisateurs</a> <span class="mx-1">/</span>
        <span class="font-semibold text-hb-blue">Nouvel utilisateur</span>
    </nav>
    <h1 class="font-display text-3xl font-bold text-hb-blue">Créer un utilisateur</h1>
    <p class="mt-1 text-sm text-gray-500">Ajoutez manuellement un compte d'hôte, de voyageur ou d'administrateur.</p>
</div>

<div class="card mx-auto max-w-2xl p-6 sm:p-8">
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
        @csrf

        @if($errors->any())
            <div class="rounded-xl bg-red-50 p-3 text-xs text-red-600">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Nom complet</label>
            <input type="text" name="name" value="{{ old('name') }}" class="input-field" required placeholder="Ex : Jean Kouassi">
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Adresse e-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" class="input-field" required placeholder="jean@exemple.com">
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Téléphone / WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="input-field" placeholder="+229 01 00 00 00 00">
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-500">Mot de passe</label>
                <input type="password" name="password" class="input-field" required minlength="8" placeholder="••••••••">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-500">Confirmation</label>
                <input type="password" name="password_confirmation" class="input-field" required minlength="8" placeholder="••••••••">
            </div>
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Profil / Rôle</label>
            <select name="role" class="input-field" required>
                <option value="voyageur" @selected(old('role') === 'voyageur')>Voyageur</option>
                <option value="hote" @selected(old('role') === 'hote')>Hôte</option>
                <option value="admin" @selected(old('role') === 'admin')>Administrateur</option>
            </select>
        </div>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-100 bg-hb-cream p-4">
            <input type="checkbox" name="is_approved" value="1" class="h-5 w-5 accent-hb-blue" checked>
            <span class="text-sm text-gray-700">
                <span class="font-semibold text-hb-blue">Compte approuvé</span>
                <span class="block text-xs text-gray-500">L'utilisateur pourra se connecter immédiatement.</span>
            </span>
        </label>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row">
            <button type="submit" class="btn-primary flex-1"><i class="bi bi-person-plus"></i> Créer l'utilisateur</button>
            <a href="{{ route('admin.users') }}" class="btn-outline flex-1"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>
@endsection
