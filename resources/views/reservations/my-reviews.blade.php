@extends('layouts.voyageur')

@section('title', 'Mes avis — Hôtels Bénin')

@section('content')
<section>
    <nav class="mb-6 flex items-center gap-1 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="{{ route('reservations.dashboard') }}" class="hover:text-hb-blue">Espace voyageur</a> <span>/</span>
        <span class="font-semibold text-hb-blue">Mes avis</span>
    </nav>

    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-hb-blue">Mes avis</h1>
        <p class="mt-1 text-sm text-gray-500">Les avis que vous avez laissés sur les chambres et établissements. Vous pouvez les supprimer à tout moment.</p>
    </div>

    @forelse($reviews as $review)
        <div class="card mb-4 p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold text-hb-blue">{{ $review->establishment->name }}
                        @if($review->room)
                            <span class="text-sm font-normal text-gray-500">· {{ $review->room->name }}</span>
                        @endif
                    </p>
                    <p class="mt-1 text-xs text-gray-400">{{ $review->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-lg text-hb-gold">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                    <form action="{{ route('reservations.reviews.destroy', $review) }}" method="POST"
                          onsubmit="return confirm('Supprimer cet avis ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-trash"></i> Supprimer</button>
                    </form>
                </div>
            </div>
            @if($review->title)
                <p class="mt-3 font-semibold text-gray-700">{{ $review->title }}</p>
            @endif
            <p class="mt-1 text-sm text-gray-600">{{ $review->comment }}</p>
        </div>
    @empty
        <div class="card p-10 text-center">
            <p class="text-lg font-semibold text-hb-blue">Vous n'avez pas encore laissé d'avis.</p>
            <p class="mt-2 text-sm text-gray-500">Après votre séjour, partagez votre expérience depuis la page de l'établissement.</p>
            <a href="{{ route('establishments.index') }}" class="btn-gold mt-5"><i class="bi bi-search"></i> Découvrir des établissements</a>
        </div>
    @endforelse

    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
</section>
@endsection
