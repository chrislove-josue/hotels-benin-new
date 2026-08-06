@extends('layouts.backoffice')

@section('title', 'Messages — Administration')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Messages de contact</h1>
    <p class="mt-1 text-sm text-gray-500">Messages reçus via le formulaire de contact.</p>
</div>

<div class="space-y-4">
    @forelse($messages as $message)
        <div class="card p-5 {{ !$message->is_read ? 'border-l-4 border-hb-orange' : '' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-semibold text-hb-blue">{{ $message->name }}</p>
                        <span class="text-xs text-gray-400">· {{ $message->email }}</span>
                        @if(!$message->is_read)
                            <span class="badge-pill bg-hb-orange/10 text-hb-orange-dark">Non lu</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
                </div>
                @if(!$message->is_read)
                    <form action="{{ route('admin.messages.read', $message->id) }}" method="POST">
                        @csrf
<button type="submit" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-envelope-open"></i> Marquer comme lu</button>
                    </form>
                @endif
            </div>

            @if($message->subject)
                <p class="mt-3 text-sm font-semibold text-gray-700">{{ $message->subject }}</p>
            @endif
            <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ $message->message }}</p>
        </div>
    @empty
        <div class="card p-10 text-center text-gray-400">Aucun message.</div>
    @endforelse
</div>

<div class="mt-6">
    {{ $messages->links() }}
</div>
@endsection
