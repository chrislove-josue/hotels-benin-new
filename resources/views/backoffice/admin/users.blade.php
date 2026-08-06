@extends('layouts.backoffice')

@section('title', 'Utilisateurs — Administration')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Utilisateurs</h1>
    <p class="mt-1 text-sm text-gray-500">Gérez les rôles et comptes de la plateforme.</p>
</div>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[680px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Utilisateur</th>
                <th class="px-5 py-3">Rôle</th>
                <th class="px-5 py-3">Établissements</th>
                <th class="px-5 py-3">Réservations</th>
                <th class="px-5 py-3">Inscrit le</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-hb-blue text-sm font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-hb-blue">{{ $user->name }}</p>
                                <p class="truncate text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="badge-pill {{ $user->role === 'admin' ? 'bg-hb-gold/20 text-hb-blue' : ($user->role === 'hote' ? 'bg-hb-orange/10 text-hb-orange-dark' : 'bg-gray-100 text-gray-600') }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $user->establishments_count }}</td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $user->reservations_count }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-3">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="flex justify-end gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="role" class="input-field !w-32 !py-1.5 text-xs">
                                @foreach(['voyageur', 'hote', 'admin'] as $role)
                                    <option value="{{ $role }}" @selected($user->role === $role)>{{ $role }}</option>
                                @endforeach
                            </select>
<button type="submit" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-check-lg"></i> OK</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucun utilisateur.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $users->links() }}
</div>
@endsection
