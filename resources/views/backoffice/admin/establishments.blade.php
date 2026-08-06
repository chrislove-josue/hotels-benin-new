@extends('layouts.backoffice')

@section('title', 'Établissements — Administration')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Établissements</h1>
        <p class="mt-1 text-sm text-gray-500">Gérez la vitrine et la disponibilité des établissements.</p>
    </div>
</div>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[720px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Établissement</th>
                <th class="px-5 py-3">Type</th>
                <th class="px-5 py-3">Localité</th>
                <th class="px-5 py-3">Prix / nuit</th>
                <th class="px-5 py-3">Propriétaire</th>
                <th class="px-5 py-3">Disponible</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($establishments as $establishment)
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $establishment->cover_image }}" alt="" class="h-10 w-14 rounded-lg object-cover">
<div class="min-w-0">
                                <p class="truncate font-semibold text-hb-blue">{{ $establishment->name }}</p>
<p class="text-xs text-gray-400">{!! $establishment->is_featured ? '<i class="bi bi-star-fill text-hb-gold"></i> À la une' : '' !!}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $establishment->type->name }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $establishment->locality->name }}</td>
                    <td class="px-5 py-3 font-semibold text-hb-blue">{{ number_format($establishment->price_from, 0, ',', ' ') }} FCFA</td>
                    <td class="px-5 py-3 text-gray-600">{{ $establishment->owner->name ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="badge-pill {{ $establishment->is_available ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $establishment->is_available ? 'Active' : 'En attente' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
<div class="flex justify-end gap-2">
                            <form action="{{ route('admin.establishments.toggle', $establishment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-outline !px-3 !py-1.5 text-xs">
                                    <i class="bi {{ $establishment->is_available ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                    {{ $establishment->is_available ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.establishments.destroy', $establishment->id) }}" method="POST" onsubmit="return confirm('Supprimer cet établissement ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucun établissement.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $establishments->links() }}
</div>
@endsection
