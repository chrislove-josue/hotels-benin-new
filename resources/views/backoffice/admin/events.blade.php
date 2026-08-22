@extends('layouts.backoffice')

@section('title', 'Événements — Administration')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Événements</h1>
        <p class="mt-1 text-sm text-gray-500">Gérez tous les événements, festivals et programmes du Bénin.</p>
    </div>
    <a href="{{ route('admin.events.create') }}" class="btn-primary"><i class="bi bi-plus-circle"></i> Nouvel événement</a>
</div>

@include('backoffice.partials.filters', [
    'action' => route('admin.events'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Titre, lieu…'],
        ['name' => 'categorie', 'label' => 'Catégorie', 'type' => 'select',
         'options' => ['' => 'Toutes les catégories', 'festival' => 'Festival', 'culture' => 'Culture & Arts', 'sport' => 'Sport', 'affaires' => 'Affaires & Conférences', 'religieux' => 'Religieux']],
        ['name' => 'visibilite', 'label' => 'Visibilité', 'type' => 'select',
         'options' => ['' => 'Toutes', 'en_ligne' => 'En ligne', 'hors_ligne' => 'Hors ligne']],
    ],
])

<div class="card overflow-x-auto">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Événement</th>
                <th class="px-5 py-3">Catégorie</th>
                <th class="px-5 py-3">Dates</th>
                <th class="px-5 py-3">Lieu</th>
                <th class="px-5 py-3">Visibilité</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($events as $event)
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($event->cover_image)
                                <img src="{{ $event->cover_image }}" alt="{{ $event->title }}" class="h-10 w-10 shrink-0 rounded-lg object-cover">
                            @else
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-hb-blue/10 text-hb-blue"><i class="bi bi-calendar-event"></i></span>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-hb-blue">{{ $event->title }}</p>
                                <p class="text-xs text-gray-400">{{ $event->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="badge-pill bg-hb-blue/10 text-hb-blue">{{ ucfirst($event->category_label) }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ optional($event->date_start)->format('d/m/Y') }}
                        @if($event->date_end && $event->date_end->toDateString() !== $event->date_start->toDateString())
                            → {{ $event->date_end->format('d/m/Y') }}
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $event->location ?? '—' }}</td>
                    <td class="px-5 py-3">
                        @if($event->is_featured)
                            <span class="badge-pill mb-1 bg-hb-gold/20 text-hb-gold"><i class="bi bi-star-fill"></i> À la une</span>
                        @endif
                        <span class="badge-pill {{ $event->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $event->is_available ? 'Visible' : 'Masqué' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('events.show', $event->slug) }}" target="_blank" class="btn-outline !px-3 !py-1.5 text-xs" title="Voir sur le site">
                                <i class="bi bi-eye"></i>
                            </a>
                            <label class="flex cursor-pointer items-center rounded-xl border border-gray-200 px-2 py-1.5 text-xs text-gray-600 hover:bg-hb-cream">
                                <input type="checkbox" class="mr-1 h-4 w-4 rounded text-hb-orange focus:ring-hb-orange"
                                    {{ $event->is_featured ? 'checked' : '' }}
                                    onclick="this.checked = !this.checked">
                                Une
                            </label>
                            <form action="{{ route('admin.events.toggle', $event->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-outline !px-3 !py-1.5 text-xs" title="Afficher/Masquer">
                                    <i class="bi {{ $event->is_available ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn-outline !px-3 !py-1.5 text-xs">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Supprimer cet événement ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-outline !px-3 !py-1.5 text-xs !border-red-200 !text-red-500 hover:!border-red-500 hover:!text-red-600">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucun événement.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $events->links() }}
</div>
@endsection

