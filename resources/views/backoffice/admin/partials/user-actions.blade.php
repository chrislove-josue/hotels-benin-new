{{-- Actions sur un utilisateur : impersonation (sauf admins), approbation, édition, suppression --}}
<div class="flex flex-wrap items-center justify-end gap-1.5">
    @unless($user->isAdmin())
        <a href="{{ route('admin.users.impersonate', $user->id) }}"
           class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 text-purple-700 transition hover:bg-purple-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-1"
           title="Ouvrir sa session (impersonation)"
           onclick="return confirm('Ouvrir la session de {{ $user->name }} ? Vous pourrez revenir à l\'espace admin à tout moment via le bandeau orange.');">
            <i class="bi bi-box-arrow-in-right text-base"></i>
        </a>
    @endunless

    @if(! $user->is_approved)
        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-green-100 text-green-700 transition hover:bg-green-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-1" title="Approuver">
                <i class="bi bi-check-circle text-base"></i>
            </button>
        </form>
    @else
        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-orange-100 text-orange-700 transition hover:bg-orange-500 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-1" title="Désactiver">
                <i class="bi bi-pause-circle text-base"></i>
            </button>
        </form>
    @endif

    <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-hb-blue/10 text-hb-blue transition hover:bg-hb-blue hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-hb-blue focus-visible:ring-offset-1" title="Modifier">
        <i class="bi bi-pencil text-base"></i>
    </a>

    @if($user->id !== auth()->id())
        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
              onsubmit="return confirm('Supprimer définitivement cet utilisateur ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-red-100 text-red-600 transition hover:bg-red-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1" title="Supprimer">
                <i class="bi bi-trash text-base"></i>
            </button>
        </form>
    @endif
</div>
