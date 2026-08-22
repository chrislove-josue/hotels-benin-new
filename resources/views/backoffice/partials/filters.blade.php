{{-- Barre de filtres réutilisable
     Variables attendues :
     - $action : URL du formulaire (route(...))
     - $fields : tableau de champs [[name, label, type=text|select, placeholder?, options?, icon?], ...]
--}}
@php
    $hasActiveFilters = collect($fields)->pluck('name')->contains(fn ($n) => request()->query($n));
@endphp

<form method="GET" action="{{ $action }}" class="card mb-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-end sm:flex-wrap">
    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:flex-wrap">
        @foreach ($fields as $field)
            @if(($field['type'] ?? 'text') === 'text')
                <div class="{{ $field['class'] ?? 'sm:min-w-[220px] sm:flex-1' }}">
                    <label class="label">{{ $field['label'] }}</label>
                    <div class="relative">
                        <i class="bi {{ $field['icon'] ?? 'bi-search' }} pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="{{ $field['name'] }}" value="{{ request($field['name']) }}"
                               placeholder="{{ $field['placeholder'] ?? '' }}" class="input-field !pl-9">
                    </div>
                </div>
            @elseif(($field['type'] ?? '') === 'select')
                <div class="{{ $field['class'] ?? '' }}">
                    <label class="label">{{ $field['label'] }}</label>
                    <select name="{{ $field['name'] }}" class="input-field">
                        @foreach ($field['options'] as $value => $label)
                            <option value="{{ $value }}" @selected(request($field['name']) === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        @endforeach
    </div>
    <div class="flex items-center gap-2 sm:shrink-0">
        <button type="submit" class="btn-primary !px-4 !py-2.5 text-sm">
            <i class="bi bi-funnel"></i> Filtrer
        </button>
        @if($hasActiveFilters)
            <a href="{{ $action }}" class="btn-outline !px-4 !py-2.5 text-sm">
                <i class="bi bi-x-circle"></i> Réinitialiser
            </a>
        @endif
    </div>
</form>
