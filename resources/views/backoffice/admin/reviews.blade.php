@extends('layouts.backoffice')

@section('title', 'Avis — Administration')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Avis clients</h1>
    <p class="mt-1 text-sm text-gray-500">Tous les avis sur les chambres et établissements — modération possible.</p>
</div>

{{-- Statistiques --}}
<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-blue/10 text-hb-blue"><i class="bi bi-chat-square-text"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500">Avis au total</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-gold/15 text-hb-gold"><i class="bi bi-star-fill"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue">{{ number_format($stats['avg'], 2) }} / 5</p>
            <p class="text-xs text-gray-500">Note moyenne</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-orange/10 text-hb-orange"><i class="bi bi-door-open"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue">{{ $stats['rooms'] }}</p>
            <p class="text-xs text-gray-500">Sur chambres</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 text-green-600"><i class="bi bi-building"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue">{{ $stats['establishments'] }}</p>
            <p class="text-xs text-gray-500">Sur établissements</p>
        </div>
    </div>
</div>

@include('backoffice.partials.filters', [
    'action' => route('admin.avis'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Voyageur, établissement, contenu…'],
        ['name' => 'note', 'label' => 'Note', 'type' => 'select',
         'options' => ['' => 'Toutes les notes', '5' => '★ 5', '4' => '★ 4', '3' => '★ 3', '2' => '★ 2', '1' => '★ 1']],
        ['name' => 'type', 'label' => 'Type', 'type' => 'select',
         'options' => ['' => 'Tous les types', 'chambre' => 'Chambres', 'etablissement' => 'Établissements']],
    ],
])

<div class="card overflow-x-auto">
    <table class="w-full min-w-[900px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Voyageur</th>
                <th class="px-5 py-3">Cible</th>
                <th class="px-5 py-3">Note</th>
                <th class="px-5 py-3">Avis</th>
                <th class="px-5 py-3">Date</th>
                <th class="px-5 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($reviews as $review)
                <tr class="hover:bg-hb-cream/50 align-top">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-hb-blue">{{ $review->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $review->user->email }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-gray-700">{{ $review->establishment->name }}</p>
                        @if($review->room)
                            <p class="text-xs text-gray-400"><i class="bi bi-door-open"></i> {{ $review->room->name }}</p>
                        @else
                            <p class="text-xs text-gray-400">Établissement général</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-hb-gold">
                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                    </td>
                    <td class="px-5 py-3 max-w-md">
                        @if($review->title)
                            <p class="font-semibold text-gray-700">{{ $review->title }}</p>
                        @endif
                        <p class="mt-0.5 text-gray-600">{{ \Illuminate\Support\Str::limit($review->comment, 160) }}</p>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-xs text-gray-500">{{ $review->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex flex-col gap-2">
                            <button type="button" class="btn-outline !px-3 !py-1.5 text-xs"
                                    @click="$dispatch('open-edit', { id: {{ $review->id }}, rating: {{ $review->rating }}, title: {{ \Illuminate\Support\Js::from($review->title) }}, comment: {{ \Illuminate\Support\Js::from($review->comment) }} })">
                                <i class="bi bi-pencil-square"></i> Modifier
                            </button>
                            <form action="{{ route('admin.avis.destroy', $review->id) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cet avis ? Cette action est définitive.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucun avis.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $reviews->links() }}
</div>

{{-- Modale de modification d'avis --}}
<div x-data="{ open: false, id: null, rating: 5, title: '', comment: '' }"
     @open-edit.window="open = true; id = $event.detail.id; rating = $event.detail.rating; title = $event.detail.title ?? ''; comment = $event.detail.comment"
     x-cloak>
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="open = false">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" @click.stop>
            <h2 class="font-display text-xl font-bold text-hb-blue"><i class="bi bi-pencil-square text-hb-gold"></i> Modifier l'avis</h2>
            <p class="mt-1 text-xs text-gray-500">Modification effectuée par l'administration — la note moyenne de l'établissement sera recalculée.</p>

            <form :action="'{{ url('admin/avis') }}/' + id" method="POST" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="mb-1 block text-sm font-semibold text-hb-blue">Note</label>
                    <select name="rating" x-model="rating" required
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-hb-gold focus:outline-none">
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ str_repeat('★', $i) }} ({{ $i }}/5)</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-hb-blue">Titre <span class="font-normal text-gray-400">(optionnel)</span></label>
                    <input type="text" name="title" x-model="title" maxlength="120"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-hb-gold focus:outline-none">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-hb-blue">Commentaire</label>
                    <textarea name="comment" x-model="comment" rows="4" required maxlength="2000"
                              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-hb-gold focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary flex-1"><i class="bi bi-check-lg"></i> Enregistrer</button>
                    <button type="button" class="btn-outline flex-1" @click="open = false">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
