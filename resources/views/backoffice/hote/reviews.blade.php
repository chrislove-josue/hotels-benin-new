@extends('layouts.backoffice')

@section('title', 'Avis — Espace hôte')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Avis reçus</h1>
    <p class="mt-1 text-sm text-gray-500">Ce que les voyageurs disent de vos établissements et chambres.</p>
</div>

{{-- Statistiques --}}
<div class="mb-6 grid grid-cols-2 gap-4">
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-blue/10 text-hb-blue"><i class="bi bi-chat-square-text"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500">Avis reçus</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-gold/15 text-hb-gold"><i class="bi bi-star-fill"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue">{{ number_format($stats['avg'], 2) }} / 5</p>
            <p class="text-xs text-gray-500">Note moyenne globale</p>
        </div>
    </div>
</div>

@include('backoffice.partials.filters', [
    'action' => route('hote.avis'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Voyageur, contenu…'],
        ['name' => 'note', 'label' => 'Note', 'type' => 'select',
         'options' => ['' => 'Toutes les notes', '5' => '★ 5', '4' => '★ 4', '3' => '★ 3', '2' => '★ 2', '1' => '★ 1']],
        ['name' => 'etablissement', 'label' => 'Établissement', 'type' => 'select',
         'options' => ['' => 'Tous mes établissements'] + $establishments->pluck('name', 'id')->all()],
    ],
])

<div class="space-y-4">
    @forelse($reviews as $review)
        <div class="card p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-hb-blue to-hb-blue-deeper font-bold text-hb-gold">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <p class="font-semibold text-hb-blue">{{ $review->user->name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $review->establishment->name }}
                            @if($review->room)
                                · {{ $review->room->name }}
                            @endif
                            · {{ $review->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <span class="whitespace-nowrap text-lg text-hb-gold">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
            </div>
            @if($review->title)
                <p class="mt-3 font-semibold text-gray-700">{{ $review->title }}</p>
            @endif
            <p class="mt-1 text-sm text-gray-600">{{ $review->comment }}</p>
        </div>
    @empty
        <div class="card p-10 text-center">
            <p class="text-lg font-semibold text-hb-blue">Aucun avis pour le moment.</p>
            <p class="mt-1 text-sm text-gray-500">Les avis de vos voyageurs s'afficheront ici après leurs séjours.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $reviews->links() }}
</div>
@endsection
