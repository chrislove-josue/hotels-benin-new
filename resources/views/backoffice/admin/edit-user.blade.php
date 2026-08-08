@extends('layouts.backoffice')

@section('title', 'Modifier un utilisateur — Administration')

@section('content')
<div class="mb-6">
    <nav class="mb-4 text-sm text-gray-500">
        <a href="{{ route('admin.users') }}" class="hover:text-hb-orange">Utilisateurs</a> <span class="mx-1">/</span>
        <span class="font-semibold text-hb-blue">{{ $user->name }}</span>
    </nav>
    <h1 class="font-display text-3xl font-bold text-hb-blue">Modifier l'utilisateur</h1>
    <p class="mt-1 text-sm text-gray-500">Mettez à jour les informations du compte {{ $user->email }}.</p>
</div>

<div class="card mx-auto max-w-2xl p-6 sm:p-8">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

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
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field" required placeholder="Ex : Jean Kouassi">
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Adresse e-mail</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required placeholder="jean@exemple.com">
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Téléphone / WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-field" placeholder="+229 01 00 00 00 00">
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-500">Nouveau mot de passe <span class="font-normal text-gray-400">(facultatif)</span></label>
                <input type="password" name="password" class="input-field" minlength="8" placeholder="Laisser vide pour conserver">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-500">Confirmation</label>
                <input type="password" name="password_confirmation" class="input-field" minlength="8" placeholder="••••••••">
            </div>
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Profil / Rôle</label>
            <select name="role" class="input-field" required>
                <option value="voyageur" @selected($user->role === 'voyageur')>Voyageur</option>
                <option value="hote" @selected($user->role === 'hote')>Hôte</option>
                <option value="admin" @selected($user->role === 'admin')>Administrateur</option>
            </select>
        </div>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-100 bg-hb-cream p-4">
            <input type="checkbox" name="is_approved" value="1" class="h-5 w-5 accent-hb-blue" @checked($user->isApproved())>
            <span class="text-sm text-gray-700">
                <span class="font-semibold text-hb-blue">Compte approuvé</span>
                <span class="block text-xs text-gray-500">Autorise l'utilisateur à accéder à son espace.</span>
            </span>
        </label>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row">
            <button type="submit" class="btn-primary flex-1"><i class="bi bi-check-lg"></i> Enregistrer</button>
            <a href="{{ route('admin.users') }}" class="btn-outline flex-1"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>
@endsection
